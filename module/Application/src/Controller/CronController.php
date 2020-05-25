<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 3/6/2017
 * Time: 12:50 PM
 */

namespace Application\Controller;


use Application\Entity\EmailTemplate;
use Application\Entity\ForumTopic;
use Application\Entity\Session;
use Application\Entity\SmsTemplate;
use Application\Entity\Test;
use Application\Model\AssignmentTable;
use Application\Model\SessionLessonAccountTable;
use Application\Model\SessionLessonTable;
use Application\Model\SessionTable;
use Application\Model\SessionTestTable;
use Application\Model\StudentSessionTable;
use Application\Model\StudentTable;
use Intermatics\HelperTrait;
use Intermatics\SmsGateway;
use Zend\Mvc\Controller\AbstractActionController;

class CronController extends AbstractController {
    use HelperTrait;
    public function indexAction()
    {
        set_time_limit(3600);
        //protect ip
        $ip = $this->getSetting('general_site_ip');
        if(!empty($ip) && trim($ip) != $_SERVER['REMOTE_ADDR']){
            exit('Unauthorized access');
        }

        //process only at 12noon in the first minute
        $hour= date('G');
        $minute = date('i');
        $cHour = $this->getSetting('general_reminder_hour');
        if($hour != $cHour ){
            exit('Invalid time for cron');
        }

        $links = ['classes','homework','courses','started','tests','forum'];
        foreach($links as $url){
            try{
                $newLink = $this->getBaseUrl().'/cron/'.$url;
                $cont = file_get_contents($newLink);
            }
            catch(\Exception $ex){

            }

            //$this->getPageAsync($newLink);
        }
        exit('cron complete');

    }


    public function forumAction(){

        $start = time()- 3600;
        $topics = ForumTopic::where('created_on','>',$start)->get();
        $sessionTopics = [];
        foreach($topics as $topic){
            $sessionTopics[$topic->session_id][] = $topic;
        }

        //now loop through session topics
        foreach($sessionTopics as $sessionId=>$topics){
            $data = [
                'topics'=>$topics,
                'controller'=>$this,
            ];
            $data['module']= 'application';
            $message = $this->bladeHtml('mails.forum_topic',$data);

            //get subscribed users
            $session = Session::find($sessionId);

            $subject = __('New Forum Topics for').' '.$session->session_name;
            $this->notifySessionStudents($sessionId,$subject,$message);

            $data['module'] = 'admin';
            $message = $this->bladeHtml('mails.forum_topic',$data);
           $sent= $this->notifyAdmins(__('New Forum Topics for').' '.Session::find($sessionId)->session_name,$message);

            //send to creator

            try{
                if(!empty($session->account_id) && !isset($sent[$session->account->email])){
                    $this->sendEmail($session->account->email,$subject,$message);
                    $sent[$session->account->email] = true;
                }
            }
            catch(\Exception $ex){

            }



            //get session instructors
            foreach($session->sessionInstructors as $instructor){
                try{
                    if(!isset($sent[$instructor->account->email])){
                        $this->sendEmail($instructor->account->email,$subject,$message);
                    }
                }
                catch(\Exception $ex){

                }


            }

        }

        exit('done');
    }

    public function classesAction(){
        $totalSent= 0;
        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());
        $studentsTable = new StudentSessionTable($this->getServiceLocator());
        $lessonAccountTable = new SessionLessonAccountTable($this->getServiceLocator());
        $rowset = $sessionLessonTable->getUpcomingLessons($this->getSetting('general_reminder_days'));

        foreach($rowset as $row){
            echo $row->lesson_name.'<br/>';
            //for this lesson, get all students enrolled
      //      $subject = 'Upcoming Class: '.$row->lesson_name;
            $class = $row->lesson_name;
            $classDate = date('d/M/Y',$row->lesson_date);
            $venue = $row->lesson_venue;
            if(empty($venue)){
                $venue =$row->venue;
            }
            $lstart = '';
            if(!empty($row->lesson_start)){
                $lstart = '<strong>'.__('Starts').':</strong> '.$row->lesson_start.'.<br/>';
            }
            $lend = '';
            if(!empty($row->lesson_end)){
                $lend = '<strong>'.__('Ends').':</strong> '.$row->lesson_end.'.<br/>';
            }

            if($row->lesson_type=='s')
            {

                $map = [
                  'CLASS_NAME'=>$class,
                  'CLASS_DATE'=>$classDate,
                    'SESSION_NAME'=>$row->session_name,
                    'CLASS_VENUE'=>$row->venue ,
                    'CLASS_START_TIME'=>$row->lesson_start,
                    'CLASS_END_TIME'=>$row->lesson_end,
                ];

                $messageArray = $this->getEmailMessage(1,$map);
                $sms = $this->getSmsMessage(1,$map);
                $subject = $messageArray['subject'];
                $message = $messageArray['message'];
                $textMessage = $sms;

//                $message = <<<EOD
//Please be reminded that the class <strong>'$class'</strong> is scheduled to hold as follows: <br/>
//<strong>Date:</strong> $classDate <br/>
//<strong>Session:</strong> $row->session_name <br/>
//<strong>Venue:</strong> $row->venue <br/>
//$lstart $lend
//EOD;
  //              $textMessage = "Reminder! The $row->session_name class '$class' holds on $classDate. Venue: $row->venue . ".strip_tags($lstart).strip_tags($lend);
            }
            else{

                $map = [
                    'CLASS_NAME'=>$class,
                    'CLASS_DATE'=>$classDate,
                    'COURSE_NAME'=>$row->session_name,
                ];

                $messageArray = $this->getEmailMessage(2,$map);
                $sms = $this->getSmsMessage(2,$map);
                $subject = $messageArray['subject'];
                $message = $messageArray['message'];
                $textMessage = $sms;



//                $message = <<<EOD
//Please be reminded that the class <strong>'$class'</strong> is scheduled as follows: <br/>
//<strong>Course:</strong> $row->session_name <br/>
//<strong>Starts:</strong> $classDate <br/>
//EOD;
//            $textMessage = "Reminder! The $row->session_name class '$class' starts on  $classDate";





            }



            //get all students in session and notify
            $students = $studentsTable->getSessionRecords(false,$row->session_id);
            $numbers= [];
            foreach($students as $student){
                try{
                    $studentMessage = setPlaceHolders($message,[
                        'RECIPIENT_FIRST_NAME'=>$student->first_name,
                        'RECIPIENT_LAST_NAME'=>$student->last_name
                    ]);
                    $studentSubject = setPlaceHolders($subject,[
                        'RECIPIENT_FIRST_NAME'=>$student->first_name,
                        'RECIPIENT_LAST_NAME'=>$student->last_name
                    ]);
                    $this->sendEmail($student->email,$studentSubject,$studentMessage);
                    if(!empty($student->mobile_number)){
                        $numbers[] = $student->mobile_number;
                    }

                    $totalSent++;
                }
                catch(\Exception $ex){}

            }

            $smsGateway = new SmsGateway($this->getServiceLocator(),$numbers,$textMessage);
            $smsGateway->send();

            //get all admins for lesson and notify
            $instructors = $lessonAccountTable->getInstructors($row->lesson_id,$row->session_id);
            foreach($instructors as $instructor){
                try{
                    $insMessage = setPlaceHolders($message,[
                        'RECIPIENT_FIRST_NAME'=>$instructor->first_name,
                        'RECIPIENT_LAST_NAME'=>$instructor->last_name
                    ]);
                    $insSubject = setPlaceHolders($subject,[
                        'RECIPIENT_FIRST_NAME'=>$instructor->first_name,
                        'RECIPIENT_LAST_NAME'=>$instructor->last_name
                    ]);
                    $this->sendEmail($instructor->email,$insSubject,$insMessage);
                    $totalSent++;
                }
                catch(\Exception $ex){}
            }


        }

        exit("Sent to: ".$totalSent);
    }

    public function testsAction(){
        $totalSent= 0;
        $sessionTestTable = new SessionTestTable($this->getServiceLocator());
        $studentsTable = new StudentSessionTable($this->getServiceLocator());
        $rowset = $sessionTestTable->getUpcomingTests($this->getSetting('general_reminder_days'));

        foreach($rowset as $row){

            $test = Test::find($row->test_id);
            $map =[
                'TEST_NAME' => $row->name,
                'TEST_DESCRIPTION'=>$test->description,
                'SESSION_NAME'=>$row->session_name,
                'OPENING_DATE'=>date('d/M/Y',$row->opening_date),
                'CLOSING_DATE'=> date('d/M/Y',$row->closing_date),
                'PASSMARK'=>$test->passmark.' %',
                'MINUTES_ALLOWED'=>$test->minutes,
            ];

            $messageArray = $this->getEmailMessage(3,$map);
            $textMessage = $this->getSmsMessage(3,$map);

            $subject= $messageArray['subject'];
            $message = $messageArray['message'];

//            echo $row->name.'<br/>';
//            //for this lesson, get all students enrolled
//            $subject = 'Upcoming Test: '.$row->name;
//            $test = $row->name;
//            $testDate = date('d/M/Y',$row->opening_date);
//
//            $lstart = '';
//            if(!empty($row->opening_date)){
//                $lstart = '<strong>Starts:</strong> '.$row->opening_date.'.<br/> ';
//            }
//            $lend = '';
//            if(!empty($row->closing_date)){
//                $lend = '<strong>Ends:</strong> '.$row->closing_date.'.<br/> Please ensure you take the test before the closing date.';
//            }
//
//
//                $message = <<<EOD
//Please be reminded that the test <strong>'$test'</strong> is scheduled as follows: <br/>
//<strong>Session/Course:</strong> $row->session_name <br/>
//$lstart $lend
//
//EOD;
//
//        $textMessage = "Reminder: The '$row->session_name' test '$test' is scheduled as follows:  ".strip_tags($lstart).strip_tags($lend);



            //get all students in session and notify
            $numbers = [];
            $students = $studentsTable->getSessionRecords(false,$row->session_id);
            foreach($students as $student){
                try{
                    $studentMessage = setPlaceHolders($message,[
                        'RECIPIENT_FIRST_NAME'=>$student->first_name,
                        'RECIPIENT_LAST_NAME'=>$student->last_name
                    ]);
                    $studentSubject = setPlaceHolders($subject,[
                        'RECIPIENT_FIRST_NAME'=>$student->first_name,
                        'RECIPIENT_LAST_NAME'=>$student->last_name
                    ]);
                    $this->sendEmail($student->email,$studentSubject,$studentMessage);

                    if(!empty($student->mobile_number)){
                        $numbers[] = $student->mobile_number;
                    }

                    $totalSent++;
                }
                catch(\Exception $ex){}

            }

            $smsGateway = new SmsGateway($this->getServiceLocator(),$numbers,$textMessage);
            $smsGateway->send();

        }

        exit("Sent to: ".$totalSent);
    }



    public function startedAction(){

        $totalSent= 0;
        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());
        $studentsTable = new StudentSessionTable($this->getServiceLocator());
        $rowset = $sessionLessonTable->getStartedLessons();

        foreach($rowset as $row){


//
//            echo $row->lesson_name.'<br/>';
//            //for this lesson, get all students enrolled
//            $subject = 'Class '.$row->lesson_name.' is open';
//            $class = $row->lesson_name;
//            $classDate = date('d/M/Y',$row->lesson_date);

             $url = $this->getBaseUrl().'/view-class/'.$row->session_id.'/'.$row->lesson_id;

//                $message = <<<EOD
//Please be reminded that the class <strong>'$class'</strong> has started. <br/>
//Click this link to take this class now: <a href="$url">$url</a><br/>
//EOD;



            $map = [
                'CLASS_NAME'=>$row->lesson_name,
                'CLASS_URL'=> $url,
                'COURSE_NAME'=> $row->session_name,
            ];


            $messageArray = $this->getEmailMessage(4,$map);
            $message = $messageArray['message'];
            $subject = $messageArray['subject'];
            $textMessage = $this->getSmsMessage(4,$map);

            //get all students in session and notify
            $students = $studentsTable->getSessionRecords(false,$row->session_id);
            $numbers = [];
            foreach($students as $student){
                try{

                    $studentMessage = setPlaceHolders($message,[
                        'RECIPIENT_FIRST_NAME'=>$student->first_name,
                        'RECIPIENT_LAST_NAME'=>$student->last_name
                    ]);
                    $studentSubject = setPlaceHolders($subject,[
                        'RECIPIENT_FIRST_NAME'=>$student->first_name,
                        'RECIPIENT_LAST_NAME'=>$student->last_name
                    ]);
                    $this->sendEmail($student->email,$studentSubject,$studentMessage);
                    if(!empty($student->mobile_number)){
                        $numbers[] = $student->mobile_number;
                    }
                    $totalSent++;
                }
                catch(\Exception $ex){}

            }

//            $textMessage= strip_tags($message);
            $smsGateway = new SmsGateway($this->getServiceLocator(),$numbers,$textMessage);
            $smsGateway->send();


        }

        exit("Sent to: ".$totalSent);
    }

    public function homeworkAction(){
        $totalSent= 0;
        $assignmentTable = new AssignmentTable($this->getServiceLocator());
        $studentsTable = new StudentSessionTable($this->getServiceLocator());
        $rowset = $assignmentTable->getUpcomingAssignments($this->getSetting('general_reminder_days'));

        foreach($rowset as $row){
            echo $row->title.'<br/>';
            $days = ($row->due_date - time())/86400;
            $days = floor($days);
            if($days>1){
                $dayText = 'days';
            }
            else{
                $dayText = 'day';
            }
            //for this lesson, get all students enrolled
            //$subject = 'Homework due in  '.$days.' '.$dayText;
            $title = $row->title;
            $dueDate = date('d/M/Y',$row->due_date);
            $openingDate = date('d/M/Y',$row->opening_date);
            $link = $this->getBaseUrl().'/submit-assignment/'.$row->assignment_id;

//                $message = <<<EOD
//Please be reminded that the homework <strong>'$title'</strong> is due on $dueDate. <br/>
//Please click this link to submit your homework now: <a href="$link">$link</a>
//EOD;

$map = [
    'NUMBER_OF_DAYS'=>$days,
    'DAY_TEXT'=>$dayText,
    'HOMEWORK_NAME'=>$title,
    'HOMEWORK_URL'=>$link,
    'HOMEWORK_INSTRUCTION'=>$row->instruction,
    'PASSMARK'=>$row->passmark,
    'DUE_DATE'=>$dueDate,
    'OPENING_DATE'=>$openingDate

];

            $messageArray = $this->getEmailMessage(5,$map);
            $textMessage = $this->getSmsMessage(5,$map);
            $message = $messageArray['message'];
            $subject = $messageArray['subject'];


            //get all students in session and notify
            $students = $studentsTable->getSessionRecords(false,$row->session_id);
            $numbers = [];
            foreach($students as $student){
                try{

                    $studentMessage = setPlaceHolders($message,[
                        'RECIPIENT_FIRST_NAME'=>$student->first_name,
                        'RECIPIENT_LAST_NAME'=>$student->last_name
                    ]);
                    $studentSubject = setPlaceHolders($subject,[
                        'RECIPIENT_FIRST_NAME'=>$student->first_name,
                        'RECIPIENT_LAST_NAME'=>$student->last_name
                    ]);
                    $this->sendEmail($student->email,$studentSubject,$studentMessage);
                    if(!empty($student->mobile_number)){
                        $numbers[] = $student->mobile_number;
                    }
                    $totalSent++;
                }
                catch(\Exception $ex){}

            }


            $smsGateway = new SmsGateway($this->getServiceLocator(),$numbers,$textMessage);
            $smsGateway->send();


        }

        exit("Sent to: ".$totalSent);


    }

    public function coursesAction(){
        $totalSent= 0;
        $sessionTable = new SessionTable($this->getServiceLocator());
        $studentsTable = new StudentSessionTable($this->getServiceLocator());
        $rowset = $sessionTable->getClosingCourses($this->getSetting('general_reminder_days'));

        foreach($rowset as $row){
            echo $row->session_name.'<br/>';
            $days = ($row->session_end_date - time())/86400;
            $days = floor($days);
            if($days>1){
                $dayText = 'days';
            }
            else{
                $dayText = 'day';
            }
            //for this lesson, get all students enrolled
//            $subject = 'Course ends in  '.$days.' '.$dayText;
            $title = $row->session_name;
            $dueDate = date('d/M/Y',$row->session_end_date);
            $link = $this->getBaseUrl().'/course-details/'.$row->session_id;

//            $message = <<<EOD
//Please be reminded that the course <strong>'$title'</strong> closes on $dueDate. <br/>
//Please click this link to complete the course now: <a href="$link">$link</a>
//EOD;



            $map = [
              'COURSE_NAME'=>$title,
                'COURSE_URL'=>$link,
                'CLOSING_DATE'=>$dueDate,
                'NUMBER_OF_DAYS'=>$days,
                'DAY_TEXT'=>$dayText,
            ];

            $messageArray = $this->getEmailMessage(6,$map);
            $textMessage = $this->getSmsMessage(6,$map);
            $message = $messageArray['message'];
            $subject = $messageArray['subject'];

            //get all students in session and notify
            $students = $studentsTable->getSessionRecords(false,$row->session_id);
            $numbers = [];
            foreach($students as $student){
                try{
                    if(empty($student->completed)){

                        $studentMessage = setPlaceHolders($message,[
                            'RECIPIENT_FIRST_NAME'=>$student->first_name,
                            'RECIPIENT_LAST_NAME'=>$student->last_name
                        ]);
                        $studentSubject = setPlaceHolders($subject,[
                            'RECIPIENT_FIRST_NAME'=>$student->first_name,
                            'RECIPIENT_LAST_NAME'=>$student->last_name
                        ]);
                        $this->sendEmail($student->email,$studentSubject,$studentMessage);
                        if(!empty($student->mobile_number)){
                            $numbers[] = $student->mobile_number;
                        }
                        $totalSent++;
                    }

                }
                catch(\Exception $ex){}

            }

           // $textMessage= strip_tags($message);
            $smsGateway = new SmsGateway($this->getServiceLocator(),$numbers,$textMessage);
            $smsGateway->send();

        }

        exit("Sent to: ".$totalSent);


    }

    private function getPageAsync($url, $params = array('noparam'=>'true'), $type='GET')
    {
        //echo "Attempting to get $url <br/>";


        foreach ($params as $key => $val) {
            if (is_array($val)) $val = implode(',', $val);
            $post_params[] = $key.'='.urlencode($val);
        }
        $post_string = implode('&', $post_params);

        $parts=parse_url($url);

        $fp = fsockopen($parts['host'],
            isset($parts['port'])?$parts['port']:80,
            $errno, $errstr, 30);

        $cusParams = $parts['query'];

        // Data goes in the path for a GET request
        if('GET' == $type) $parts['path'] .= '?'.$cusParams;

        $out = "$type ".$parts['path']." HTTP/1.1\r\n";
        $out.= "Host: ".$parts['host']."\r\n";
        $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out.= "Content-Length: ".strlen($post_string)."\r\n";
        $out.= "Connection: Close\r\n\r\n";
        // Data goes in the request body for a POST request
        if ('POST' == $type && isset($post_string)) $out.= $post_string;

        fwrite($fp, $out);
        fclose($fp);
    }

    private function getEmailMessage($id,$map){
        $template = EmailTemplate::find($id);
        $message = $template->message;
        $subject = $template->subject;
        foreach ($map as $key=>$value){
            $key = '['.$key.']';
            $message = str_replace($key,$value,$message);
            $subject = str_replace($key,$value,$subject);
        }

        return [
            'message'=>$message,
            'subject'=>$subject
        ];

    }

    private function getSmsMessage($id,$map){
        $template = SmsTemplate::find($id);
        $message = $template->message;
        foreach ($map as $key=>$value){
            $key = '['.$key.']';
            $message = str_replace($key,$value,$message);
        }

        return strip_tags($message);
    }


}