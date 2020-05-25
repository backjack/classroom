<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/27/2017
 * Time: 7:05 PM
 */

namespace Admin\Controller;


use Application\Controller\AbstractController;
use Application\Entity\Lecture;
use Application\Entity\Lesson;
use Application\Entity\Session;
use Application\Entity\Video;
use Application\Form\DiscussionForm;
use Application\Model\AccountsTable;
use Application\Model\AssignmentSubmissionTable;
use Application\Model\AssignmentTable;
use Application\Model\AttendanceTable;
use Application\Model\BookmarkTable;
use Application\Model\DiscussionAccountTable;
use Application\Model\DiscussionTable;
use Application\Model\DownloadFileTable;
use Application\Model\DownloadSessionTable;
use Application\Model\LectureFileTable;
use Application\Model\LecturePageTable;
use Application\Model\LectureTable;
use Application\Model\LessonFileTable;
use Application\Model\LessonTable;
use Application\Model\SessionInstructorTable;
use Application\Model\SessionLessonTable;
use Application\Model\SessionTable;
use Application\Model\StudentLectureTable;
use Application\Model\StudentSessionLogTable;
use Application\Model\StudentSessionTable;
use Application\Model\StudentTestTable;
use Application\Model\TestTable;
use Intermatics\HelperTrait;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class CourseController extends AbstractController {

    use HelperTrait;

    public function setEventManager(EventManagerInterface $events)
    {

        parent::setEventManager($events);
        $controller = $this;
        $events->attach('dispatch', function ($e) use ($controller) {
            $controller->layout('layout/studentdemo');
        }, 100);
    }




    private function validateEnrollment($sessionId){

/*        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        if(!$studentSessionTable->enrolled($this->getId(),$sessionId)){
            $this->flashMessenger()->addMessage('You are not enrolled into this course');
            $this->redirect()->toRoute('courses');
            // $this->goBack();
        }*/

        $sessionTable = new SessionTable($this->getServiceLocator());
        $row = $sessionTable->getRecord($sessionId);
        if(!empty($row->enable_chat)){
            //get the live chat code from settings an place in constant here
            define('ENABLE_CHAT',true);
        }
    }

    public function introAction(){
        $id = $this->params('id');
        $this->validateEnrollment($id);
        $sessionTable = new SessionTable($this->getServiceLocator());
        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());
        $lectureTable = new LectureTable($this->getServiceLocator());
        $row = $sessionTable->getRecord($id);

        $downloadSessionTable = new DownloadSessionTable($this->getServiceLocator());
        $downloads = $downloadSessionTable->getSessionRecords($id);
        $instructorsTable = new SessionInstructorTable($this->getServiceLocator());

        //get class list
        $classes = $sessionLessonTable->getSessionRecords($id,'c');
        $classes->buffer();

        //get discussions for course
        $discussionTable = new DiscussionTable($this->getServiceLocator());
        $discussionForm = new DiscussionForm(null,$this->getServiceLocator(),$this->getId());
        $discussions = $discussionTable->getPaginatedRecordsForStudent(false,$this->getId(),$id);

        $instructors = $instructorsTable->getSessionRecords($id);
        $discussionForm->remove('account_id[]');
        $options = [];
        $options['admins'] = __('Administrators');
        foreach($instructors as $row2){

            $options[$row2->account_id]= $row2->first_name.' '.$row2->last_name;


        }


        $discussionForm->createSelect('account_id[]','Recipients (Admins/Instructors)',$options,true);
        $discussionForm->get('account_id[]')->setAttribute('multiple','multiple');
        $discussionForm->get('account_id[]')->setAttribute('class','form-control select2');

        //get first class
        $firstClass = $classes->current();
        if($firstClass){
            $classLink =  $this->url()->fromRoute('view-class-demo',['classId'=>$firstClass->lesson_id,'sessionId'=>$firstClass->session_id]);
            if($row->session_type!='c'){
                $this->redirect()->toUrl($classLink);
            }
        }
        else{
            $classLink = '';
        }


        //calculate progress of attendance
        $attendanceTable = new AttendanceTable($this->getServiceLocator());
        $totalClasses = $sessionLessonTable->getSessionRecords($id)->count();
        //get tottal attended
        $attended = $attendanceTable->getTotalDistinctForStudentInSession($this->getId(),$id);

        if(!empty($totalClasses)){
            $percentage= ($attended/$totalClasses) * 100;
        }
        else{
            $percentage=0;
        }

        $attendanceRecords = $attendanceTable->getAttendedRecords($this->getId(),$id);






        $output= [
            'pageTitle'=>$row->session_name.': '.__('Introduction'),
            'classes'=>$classes,
            'lectureTable'=>$lectureTable,
            'sessionRow'=>$row,
            'downloads'=>$downloads,
            'fileTable'=> new DownloadFileTable($this->getServiceLocator()),
            'discussions'=> $discussions,
            'form' => $discussionForm,
            'classLink' => $classLink,
            'sessionId'=>$id,
            'instructors'=>$instructors,
            'accountTable'=>new DiscussionAccountTable($this->getServiceLocator()),
            'percentage'=>$percentage,
            'attendanceTable'=>$attendanceTable,
            'sessionLogTable'=> new StudentSessionLogTable($this->getServiceLocator()),
            'studentId'=> $this->getId(),
            'attendanceRecords'=>$attendanceRecords,
            'totalClasses'=>$totalClasses
        ];

        $output['customCrumbs'] = [
            $this->url()->fromRoute('home') =>__('Home'),
            $this->url()->fromRoute('courses')=>__('Online Courses'),
            $this->url()->fromRoute('course-details',['id'=>$row->session_id,'slug'=>safeUrl($row->session_name)])=>__('Course Details'),
             '#'=>__('Introduction')
        ];


        //get forum topics
        $bladeData['lecture'] = null;
        $bladeData['topics'] = Session::find($id)->forumTopics()->orderBy('forum_topic_id','desc')->paginate(50);
        $bladeData['id'] = $id;
        $bladeData['target'] = '_blank';
        $output['forumTopics'] = '';

        $viewModel = new ViewModel($output);
        $viewModel->setTemplate('application/course/intro');
        return $viewModel;
    }

    public function classAction(){
        $classId = $this->params('classId');
        $sessionId = $this->params('sessionId');

        $this->verifyClass($classId,$sessionId);

        $lessonTable = new LessonTable($this->getServiceLocator());
        $lesson = $lessonTable->getRecord($classId);

        //check if the enforce order flag is selected


        //check if the class is opened yet



        //get list of lectures
        $lectureTable = new LectureTable($this->getServiceLocator());
        $lectures = $lectureTable->getRecordsOrdered($classId);
        $lectures->buffer();
        $lecturePageTable = new LecturePageTable($this->getServiceLocator());
        $downloadsTable = new LessonFileTable($this->getServiceLocator());
        $downloads = $downloadsTable->getDownloadRecords($classId);
        $downloads->buffer();

        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());

        //get previous class link
        $previousClass = $sessionLessonTable->getPreviousLessonInSession($sessionId,$classId,'c');
        if($previousClass){
            $previous = $this->url()->fromRoute('view-class-demo',['classId'=>$previousClass->lesson_id,'sessionId'=>$sessionId]);

        }
        else{
            $previous = false;
        }

        if($lectureTable->getTotalLectures($classId)>0){
            $nextRow = $lectures->current();
            $next = $this->url()->fromRoute('view-lecture-demo',['lectureId'=>$nextRow->lecture_id,'sessionId'=>$sessionId]);

        }
        else{
            $next = false;
        }

        $output= [
            'pageTitle'=>__('Class').' : '.$lesson->lesson_name,
            'lectures'=>$lectures,
            'lectureTable'=>$lectureTable,
            'classRow'=>$lesson,
            'downloads'=>$downloads,
            'fileTable'=> $downloadsTable,
            'previous'=>$previous,
            'next'=>$next,
            'lecturePageTable'=>$lecturePageTable,
            'sessionId'=>$sessionId
        ];

        $output['customCrumbs'] = [
            $this->url()->fromRoute('home') =>__('Home'),
            $this->url()->fromRoute('courses')=>__('Online Courses'),
            $this->url()->fromRoute('course-details',['id'=>$sessionId,'slug'=>safeUrl($this->getSession($sessionId)->session_name)])=>__('Course Details'),
            $this->url()->fromRoute('admin/default', ['controller' => 'course', 'action' => 'intro','id'=>$sessionId])=>__('Introduction'),
            '#'=>__('Class Details')
        ];
        $viewModel = new ViewModel($output);
        $viewModel->setTemplate('application/course/class');
        return $viewModel;
    }


    public function lectureAction(){

        $lectureId = $this->params('lectureId');
        $sessionId = $this->params('sessionId');
        $this->checkLectureOrder($lectureId,$sessionId);

        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());
        $lectureTable = new LectureTable($this->getServiceLocator());
        $studentLectureTable = new StudentLectureTable($this->getServiceLocator());
        $lecture = $lectureTable->getRecord($lectureId);
        $this->verifyClass($lecture->lesson_id,$sessionId);
        //$studentLectureTable->logLecture($this->getId(),$sessionId,$lectureId);

        $lecturePageTable = new LecturePageTable($this->getServiceLocator());
        $pages = $lecturePageTable->getRecordsOrdered($lectureId);
        $pages->buffer();

        $fileTable = new LectureFileTable($this->getServiceLocator());
        $downloads = $fileTable->getDownloadRecords($lectureId);
        $downloads->buffer();
        //get previous and next links
        $previous = $lectureTable->getPreviousLecture($lectureId);
        $next = $lectureTable->getNextLecture($lectureId);
        $previousLesson = $sessionLessonTable->getPreviousLessonInSession($sessionId,$lecture->lesson_id,'c');


        $sessionInstructorTable = new SessionInstructorTable($this->getServiceLocator());
        $form = new DiscussionForm(null,$this->getServiceLocator(),$this->getId());
        $instructors = $sessionInstructorTable->getSessionRecords($sessionId);

        $form->remove('account_id[]');
        $options = [];
        $options['admins'] = __('Administrators');
        foreach($instructors as $row){

                $options[$row->account_id]= $row->first_name.' '.$row->last_name;


        }


        $form->createSelect('account_id[]',__('Recipients').' ('.__('Admins/Instructors').')',$options,true);
        $form->get('account_id[]')->setAttribute('multiple','multiple');
        $form->get('account_id[]')->setAttribute('class','form-control select2');


        $discussionTable = new DiscussionTable($this->getServiceLocator());
        $discussions = $discussionTable->getPaginatedRecordsForStudent(false,$this->getId(),$sessionId,$lectureId);

        //get all lectures for this lecture's class
        $lectures = $lectureTable->getRecordsOrdered($lecture->lesson_id);

        $sessionEntity = Session::find($sessionId);
        $output = [
          'pageTitle'=>__('Class').': '.$lecture->lesson_name,
            'pages'=>$pages,
            'lecture'=>$lecture,
            'downloads'=>$downloads,
            'previous'=>$previous,
            'next'=>$next,
            'form'=>$form,
            'instructors'=>$instructors,
            'discussions'=>$discussions,
            'sessionRow'=>$this->getSession($sessionId),
            'fileTable'=>$fileTable,
            'totalPages'=>$pages->count(),
            'sessionId'=>$sessionId,
            'accountTable'=>new DiscussionAccountTable($this->getServiceLocator()),
            'pageTable'=> $lecturePageTable,
            'previousLesson'=>$previousLesson,
            'lecturePageTable'=>$lecturePageTable,
            'lectures'=>$lectures,
            'session'=>$sessionEntity
        ];

        $output['customCrumbs'] = [
            $this->url()->fromRoute('home') =>__('Home'),
            $this->url()->fromRoute('courses')=>__('Online Courses'),
            $this->url()->fromRoute('course-details',['id'=>$sessionId,'slug'=>safeUrl($this->getSession($sessionId)->session_name)])=>__('Course Details'),
            $this->url()->fromRoute('admin/default', ['controller' => 'course', 'action' => 'intro','id'=>$sessionId])=>__('Introduction'),
            $this->url()->fromRoute('view-class-demo',['classId'=>$lecture->lesson_id,'sessionId'=>$sessionId])=>__('Class Details'),
            '#'=>__('Lecture')
        ];

        $bladeData['lecture'] = null;
        $bladeData['topics'] = Session::find($sessionId)->forumTopics()->orderBy('forum_topic_id','desc')->paginate(50);
        $bladeData['id'] = $sessionId;
        $bladeData['target'] = '_blank';
        $output['forumTopics'] = '';



        //check if there is a video in content
        $lecture= Lecture::find($lectureId);
        foreach ($lecture->lecturePages as $page){
            if($page->type =='l' && defined('USER_ID')){
                $videoId = intval($page->content);
                $video = Video::find($videoId);
                $s3Path = USER_ID.'/'.$video->video_id.'/playlist.m3u8';
                try {


                    $config = $this->getServiceManager()->get('config');
                    $s3Config = $config['s3'];
                    $expires = time() + (86400 * 4);
                    $cloudFront = $this->getCloudFrontClient();

                    if(!isTrainEasySubdomain() && defined('BASE_DOMAIN_NAME') && !empty(BASE_DOMAIN_NAME)){
                        $s3Config['cloudfront_domain'] = 'vcdn.'.trim(BASE_DOMAIN_NAME);
                        $cookie= BASE_DOMAIN_NAME;
                    }
                    else{
                        $cookie = 'traineasy.net';
                    }

                    $resourceKey = 'http://' . $s3Config['cloudfront_domain'] . '/' . USER_ID . '/' . $video->video_id . '/*';

                    //$resourceKey = 'http://vcdn.traineasy.net/*';

                    $customPolicy = '{"Statement":[{"Resource":"' . $resourceKey . '","Condition":{"DateLessThan":{"AWS:EpochTime":' . $expires . '}}}]}';

                    $signedCookieCannedPolicy = $cloudFront->getSignedCookie([
                        'policy' => $customPolicy,
                        'expires' => $expires,
                        'key_pair_id' => $s3Config['cloudfront_key_pair_id'],
                        'private_key' => 'config/pk-APKAISJFXUOFGEQFNX5A.pem'
                    ]);

                    foreach ($signedCookieCannedPolicy as $name => $value) {
                        $output['cookie'][] = ['name' => $name, 'value' => $value];
                        //  setrawcookie($name, $value, time() + (86400 * 30), "/",'traineasy.net', false, false); // 86400 = 1 day
                       // setrawcookie($name, $value, 0, "/", "traineasy.net", false, false);

                        setrawcookie($name, $value, 0, "/", $cookie, false, false);

                    }


                    $response = $this->getResponse();
                    $response->getHeaders()->addHeaderLine('Access-Control-Allow-Origin', '*');
                    $response->getHeaders()->addHeaderLine('Access-Control-Allow-Credentials', 'true');

                    $output['videoUrl'] = 'http://' . $s3Config['cloudfront_domain'] . '/' . $s3Path;
                }
                catch (\Exception $ex){

                }
            }
        }


        //put demo
        $output['module']= 'admin';
        $output['append'] = '-demo';
        $viewModel = new ViewModel($output);
        $viewModel->setTerminal(true);
        $viewModel->setTemplate('application/course/lecture');
        return $viewModel;

    }

    public function bookmarkAction(){
        exit(json_encode(array('status'=>true,'message'=>__('Bookmark added'))));

        if($this->request->isPost()){

            $lecturePageId = $this->request->getPost('id');
            $sessionId = $this->request->getPost('session_id');
            $lecturePageTable = new LecturePageTable($this->getServiceLocator());
            $lectureTable = new LectureTable($this->getServiceLocator());
            $pageRow = $lecturePageTable->getRecord($lecturePageId);
            $lectureRow = $lectureTable->getRecord($pageRow->lecture_id);
            $this->verifyClass($lectureRow->lesson_id,$sessionId);

            $bookMarkTable = new BookmarkTable($this->getServiceLocator());

            if($bookMarkTable->addBookMark($this->getId(),$lecturePageId,$sessionId)){
                $status = true;
                $message= 'Bookmark Added';
            }
            else{
                $status = false;
                $message = 'Bookmark already exists';
            }
            exit(json_encode(array('status'=>$status,'message'=>$message)));
        }
        return $this->goBack();
    }

    private function checkOrder($classId,$sessionId){
        $session = $this->getSession($sessionId);
        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());
        $lesson = $sessionLessonTable->getSessionLessonRecord($sessionId,$classId);
        if(!empty($session->enforce_order) && $lesson->sort_order > 1){

            $attendanceTable = new AttendanceTable($this->getServiceLocator());
            if($attendanceTable->hasAttendance($this->getId(),$classId,$sessionId)){
                return true;
            }
                //get previous class
            $previousClass = $sessionLessonTable->getPreviousLessonInSession($sessionId,$classId);
            if(!$attendanceTable->hasAttendance($this->getId(),$previousClass->lesson_id,$sessionId)){
                //get the last class student attended
                $lessons = $sessionLessonTable->getSessionRecords($sessionId);

                //getStudentLog

                $nextLesson = null;
                foreach($lessons as $lesson){

                    if(!$attendanceTable->hasAttendance($this->getId(),$lesson->lesson_id,$sessionId)){

                        $nextLesson = $lesson->lesson_id;
                        break;
                    }

                }//end lessons loop
                $this->flashMessenger()->addMessage(__('complete-right-order'));

                if($nextLesson){
                    $this->redirect()->toRoute('view-class',['sessionId'=>$sessionId,'classId'=>$nextLesson]);
                }
                else{
                    $this->goBack();
                }
            }

        }

    }

    private function checkLectureOrder($lectureId,$sessionId){
        return true;
        $session = $this->getSession($sessionId);
        $lectureTable= new LectureTable($this->getServiceLocator());
        $sessionLogTable = new StudentSessionLogTable($this->getServiceLocator());
        $lectureRow = $lectureTable->getRecord($lectureId);
        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());
        $lessonTable = new LessonTable($this->getServiceLocator());
        $classId = $lectureRow->lesson_id;
        $lesson = $lessonTable->getRecord($classId);
        if(!empty($lesson->enforce_lecture_order) && $lectureRow->sort_order > 1){

            if($sessionLogTable->hasAttendance($this->getId(),$sessionId,$lectureId)){
                return true;
            }
            //get previous class
            $previousLecture = $lectureTable->getPreviousLecture($lectureId);
            if(!$sessionLogTable->hasAttendance($this->getId(),$sessionId,$previousLecture->lecture_id)){
                //get the last lecture student attended
                $lectures = $lectureTable->getRecordsOrdered($classId);
                //getStudentLog
                $nextLecture = null;
                foreach($lectures as $lecture){

                    if(!$sessionLogTable->hasAttendance($this->getId(),$sessionId,$lecture->lecture_id)){

                        $nextLecture = $lecture->lecture_id;
                        break;
                    }

                }//end lessons loop
                $this->flashMessenger()->addMessage(__('complete-right-order'));

                if($nextLecture){
                    $this->redirect()->toRoute('view-lecture',['sessionId'=>$sessionId,'lectureId'=>$nextLecture]);
                }
                else{
                    $this->goBack();
                }
            }

        }

    }
    private function getSession($id){
        $sessionTable= new SessionTable($this->getServiceLocator());
        $session = $sessionTable->getRecord($id);
        return $session;
    }
    private function verifyClass($id,$session){
        return true;
        $sessionContainer = new Container('course');
        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());
        $this->validateEnrollment($session);
        $this->checkOrder($id,$session);

        //check previous class and see if there is any outstanding assignment
        $previousClass = $sessionLessonTable->getPreviousLessonInSession($session,$id,'c');
        if($previousClass){

            //get assignments
            $assignementTable = new AssignmentTable($this->getServiceLocator());
            $assignments = $assignementTable->getSessionLessonAssignments($session,$previousClass->lesson_id);
            $assignments->buffer();
            $assignmentSubmissionTable = new AssignmentSubmissionTable($this->getServiceLocator());
            $total = $assignments->count();
            $firstAssignment = false;
            if(!empty($total)){
                //assignments exist
                $sessionContainer->url = selfURL();

                //loop through assignments and verify student has submission for each
                foreach($assignments as $assignment){


                    if(!$assignmentSubmissionTable->hasSubmission($this->getId(),$assignment->assignment_id)){
                        if($firstAssignment==false){
                            $firstAssignment = $assignment;
                        }

                        $subject = 'You have Homework for '.$previousClass->lesson_name;
$message= 'You are required to complete the homework \''.$assignment->title.'\' for the \' '.$previousClass->lesson_name.' \' class. <br/>
Please view and submit the Homework here: '.$this->absoluteRoute('application/submit-assignment',['id'=>$assignment->assignment_id]);

                      //  $this->notifyStudent($this->getId(),$subject,$message);

                    }
                }

                if($firstAssignment){
                    //redirect to assignment page
                    $this->flashMessenger()->addMessage('You are required to complete the homework \''.$firstAssignment->title.'\' for the \' '.$previousClass->lesson_name.' \' class.');

                  //  return $this->redirect()->toRoute('application/submit-assignment',['id'=>$firstAssignment->assignment_id]);

                }


            }

        }

        //check that class is opened
        $lessonRow = $sessionLessonTable->getSessionLessonRecord($session,$id);
        if(!empty($lessonRow->lesson_date) && $lessonRow->lesson_date > time()){
            $lesson = Lesson::find($lessonRow->lesson_id);
            $this->flashMessenger()->addMessage(__('class-scheduled-start',['className'=>$lesson->lesson_name,'start'=>date('d/M/Y',$lessonRow->lesson_date)]));

           // $this->redirect()->toRoute('course-details',['id'=>$session]);
            $this->redirect()->toRoute('admin/default',['controller'=>'course','action'=>'intro','id'=>$session]);
        }


        if($sessionLessonTable->lessonExists($session,$id)){
            return true;
        }
        else{
            exit('Invalid record');
        }

    }


    /**
     * This action is for logging a lectures attendance
     */
    public function loglectureAction(){

        $sessionLogTable = new StudentSessionLogTable($this->getServiceLocator());
        $studentTestTable = new StudentTestTable($this->getServiceLocator());
        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());
        $testTable = new TestTable($this->getServiceLocator());
        $studentLectureTable = new StudentLectureTable($this->getServiceLocator());
        $container = new Container('classTest');
        if($this->request->isPost())
        {

            $lecture = $this->request->getPost('lecture_id');
            $session = $this->request->getPost('session_id');

            $lectureTable = new LectureTable($this->getServiceLocator());
            $lectureRow = $lectureTable->getRecord($lecture);
            $lessonTable = new LessonTable($this->getServiceLocator());
            $this->verifyClass($lectureRow->lesson_id,$session);

       /*     $logId = $sessionLogTable->addRecord([
                'student_id'=>$this->getId(),
                'session_id'=>$session,
                'lecture_id'=>$lecture,
                'log_date'=>time()
            ]);*/

            //check if there is another lecture and redirect to it
            $next = $lectureTable->getNextLecture($lecture);
            if($next){
                $this->redirect()->toRoute('view-lecture-demo',['lectureId'=>$next->lecture_id,'sessionId'=>$session]);
            }
            else{
                //check for outstanding lectures
            /*    $allLectures = $lectureTable->getLectureRecords($lectureRow->lesson_id);
                foreach($allLectures as $row){

                    if(!$sessionLogTable->hasAttendance($this->getId(),$session,$row->lecture_id)){
                        $this->flashMessenger()->addMessage('You have outstanding lectures. Please complete all lectures in this class');
                        $this->redirect()->toRoute('view-lecture-demo',['lectureId'=>$row->lecture_id,'sessionId'=>$session]);
                    }

                }*/



                //check if class has test
                $lessonRow = $lessonTable->getRecord($lectureRow->lesson_id);
                if(false && !empty($lessonRow->test_required) && !empty($lessonRow->test_id) && $testTable->recordExists($lessonRow->test_id) && !$studentTestTable->passedTest($this->getId(),$lessonRow->test_id)){

                    $container->testInfo = serialize([$lessonRow->test_id=>[
                        'lesson_id'=>  $lectureRow->lesson_id,
                        'session_id'=>$session,
                        'lesson_name'=>$lessonRow->lesson_name
                    ]]);


                   // $this->flashMessenger()->addMessage('You are required to take this test in order to complete the \''.$lessonRow->lesson_name.'\' class');
                   // $this->redirect()->toRoute('application/taketest',['id'=>$lessonRow->test_id]);
                }
                else{

                    //log attendance for this class
                    $attendanceTable = new AttendanceTable($this->getServiceLocator());
               /*     $attendanceTable->setAttendance([
                        'student_id'=>$this->getId(),
                        'session_id'=>$session,
                        'lesson_id'=>$lectureRow->lesson_id
                    ]);*/

                    //get next class
                    $nextClass = $sessionLessonTable->getNextLessonInSession($session,$lectureRow->lesson_id,'c');
                    if($nextClass){
                        //forward to the next class
                        $this->redirect()->toRoute('view-class-demo',['sessionId'=>$session,'classId'=>$nextClass->lesson_id]);
                    }
                    else{
                        //classes are over
                       // $studentLectureTable->clearLecture($this->getId(),$session);
                        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
                       // $studentSessionTable->markCompleted($this->getId(),$session);
                        $this->flashMessenger()->addMessage(__('course-complete-msg'));
                        $this->redirect()->toRoute('course-details',['id'=>$session]);
                    }

                }

            }

        }
        else{
            $this->goBack();
        }
    }




    public function classfileAction(){
        set_time_limit(86400);
        $id = $this->params('id');
        $sessionId = $this->params('sessionId');
        $table = new LessonFileTable($this->getServiceLocator());
        $row = $table->getRecord($id);

        $this->verifyClass($row->lesson_id,$sessionId);

        $path = 'public/usermedia/'.$row->path;



        header('Content-type: '.getFileMimeType($path));

// It will be called downloaded.pdf
        header('Content-Disposition: attachment; filename="'.basename($path).'"');

// The PDF source is in original.pdf
        readfile($path);
        exit();
    }


    public function allclassfilesAction(){
        set_time_limit(86400);
        $id = $this->params('id');
        $sessionId = $this->params('sessionId');
        $this->verifyClass($id,$sessionId);
        $downloadTable = new LessonTable($this->getServiceLocator());
        $downloadFileTable= new LessonFileTable($this->getServiceLocator());
        $rowset = $downloadFileTable->getDownloadRecords($id);
        $downloadRow = $downloadTable->getRecord($id);

        $zipname = safeUrl($downloadRow->lesson_name).'_resources.zip';
        $zip = new \ZipArchive;
        $zip->open($zipname, \ZipArchive::CREATE);
        $count = 1;
        $deleteFiles = [];
        foreach ($rowset as $row) {
            $path = 'public/usermedia/' . $row->path;

            if (file_exists($path)) {
                $newFile = $count.'-'.basename($path);
                copy($path,$newFile);
                $zip->addFile($newFile);
                $count++;
                $deleteFiles[] = $newFile;
            }



        }
        $zip->close();

        foreach($deleteFiles as $value){
            unlink($value);
        }

        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename='.$zipname);
        header('Content-Length: ' . filesize($zipname));
        readfile($zipname);
        unlink($zipname);
        exit();
    }

    public function lecturefileAction(){
        set_time_limit(86400);
        $id = $this->params('id');
        $sessionId = $this->params('sessionId');
        $table = new LectureFileTable($this->getServiceLocator());
        $row = $table->getRecord($id);

        //get lecture table
        $lectureTable = new LectureTable($this->getServiceLocator());
        $lecture = $lectureTable->getRecord($row->lecture_id);

        $this->verifyClass($lecture->lesson_id,$sessionId);

        $path = 'public/usermedia/'.$row->path;



        header('Content-type: '.getFileMimeType($path));

// It will be called downloaded.pdf
        header('Content-Disposition: attachment; filename="'.basename($path).'"');

// The PDF source is in original.pdf
        readfile($path);
        exit();
    }


    public function alllecturefilesAction(){
        set_time_limit(86400);
        $id = $this->params('id');
        $sessionId = $this->params('sessionId');

        $downloadTable = new LectureTable($this->getServiceLocator());
        $downloadFileTable= new LectureFileTable($this->getServiceLocator());
        $rowset = $downloadFileTable->getDownloadRecords($id);
        $downloadRow = $downloadTable->getRecord($id);
        $this->verifyClass($downloadRow->lesson_id,$sessionId);
        $zipname = safeUrl($downloadRow->lecture_title).'_resources.zip';
        $zip = new \ZipArchive;
        $zip->open($zipname, \ZipArchive::CREATE);
        $count = 1;
        $deleteFiles = [];
        foreach ($rowset as $row) {
            $path = 'public/usermedia/' . $row->path;

            if (file_exists($path)) {
                $newFile = $count.'-'.basename($path);
                copy($path,$newFile);
                $zip->addFile($newFile);
                $count++;
                $deleteFiles[] = $newFile;
            }



        }
        $zip->close();

        foreach($deleteFiles as $value){
            unlink($value);
        }

        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename='.$zipname);
        header('Content-Length: ' . filesize($zipname));
        readfile($zipname);
        unlink($zipname);
        exit();
    }

    public function bookmarksAction(){
        $studentId = $this->getId();
        $bookmarkTable = new BookmarkTable($this->getServiceLocator());
        $paginator = $bookmarkTable->getPaginatedStudentRecords(true,$studentId);
        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);

        $output = [
            'pageTitle'=>__('My Bookmarks'),
            'paginator'=>$paginator,
            'id'=>$studentId,
        ];
        return new ViewModel($output);
    }

    public function getId(){
        return null;
    }

}