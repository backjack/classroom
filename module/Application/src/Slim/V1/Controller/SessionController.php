<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 11/22/2018
 * Time: 10:23 AM
 */

namespace Application\Slim\V1\Controller;

use Application\Entity\Bookmark;
use Application\Entity\Lecture;
use Application\Entity\LectureFile;
use Application\Entity\LecturePage;
use Application\Entity\Lesson;
use Application\Entity\Session;
use Application\Entity\Student;
use Application\Entity\Video;
use Application\Model\AssignmentSubmissionTable;
use Application\Model\AssignmentTable;
use Application\Model\AttendanceTable;
use Application\Model\DiscussionTable;
use Application\Model\DownloadSessionTable;
use Application\Model\LectureFileTable;
use Application\Model\LecturePageTable;
use Application\Model\LectureTable;
use Application\Model\LessonFileTable;
use Application\Model\LessonTable;
use Application\Model\SessionInstructorTable;
use Application\Model\SessionLessonAccountTable;
use Application\Model\SessionLessonTable;
use Application\Model\SessionTable;
use Application\Model\SessionTestTable;
use Application\Model\StudentLectureTable;
use Application\Model\StudentSessionLogTable;
use Application\Model\StudentSessionTable;
use Application\Model\StudentTestTable;
use Application\Model\StudentVideoTable;
use Application\Model\TestTable;
use Intermatics\HelperTrait;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class SessionController extends Controller {

use HelperTrait;
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }
    public function getSessions(Request $request,Response $response,$args){

        $params = $request->getQueryParams();

        $table = new SessionTable();

        $filter = null;

        if (isset($params['filter']) && !empty($params['filter'])) {
            $filter=$params['filter'];
        }

        
        $group = null;
        if (isset($params['group']) && !empty($params['group'])) {
            $group=$params['group'];
        }

        $sort = null;
        if (isset($params['sort']) && !empty($params['sort'])) {
            $sort=$params['sort'];
        }


        $type = null;
        if(isset($params['type']) && !empty($params['type'])){

            $type = explode('-',$params['type']);
        }

        if(isset($params['rows']) && !empty($params['rows']) && $params['rows'] <= 100 ){
            $rowsPerPage = $params['rows'];
        }
       else{
            $rowsPerPage = 30;
        }

        $currentPage = (int) (empty($params['page'])? 1 : $params['page']);


        $paginator = $table->getPaginatedRecords(true,null,true,$filter,$group,$sort,$type,true);
        $paginator->setCurrentPageNumber($currentPage);
        $paginator->setItemCountPerPage($rowsPerPage);

        $total = $table->getTotalRecords(true,null,true,$filter,$group,$sort,$type,true);


        $output = [
            'current_page'=>(int) (empty($params['page'])? 1 : $params['page']),
            'rows_per_page'=> $rowsPerPage,
            'total' => $total
        ];
        if($total==0){
            $total = 1;
        }
        //check for exceeded page
        $totalPages = ceil($total/$rowsPerPage);
        if($currentPage > $totalPages){
            $output['records'] = [];
            return jsonResponse($output);
        }

        foreach($paginator as $row){
            $data = Session::find($row->session_id)->toArray();
            if(isset($params['currency_id'])){
                $data['amount'] = price($data['amount'],$params['currency_id'],true);
            }

           $output['records'][]=$data;

        }

        return jsonResponse($output);
    }

    public function getCourses(Request $request,Response $response,$args){


        
        $params = $request->getQueryParams();

        $table = new SessionTable();

        $filter = null;

        if (isset($params['filter'])) {
            $filter=$params['filter'];
        }

        $group = null;
        if (isset($params['group'])) {
            $group=$params['group'];
        }

        $sort = null;
        if (isset($sort)) {
            $sort=$params['sort'];
        }


        if(isset($params['rows'])){
            $rowsPerPage = $params['rows'];
        }
        else{
            $rowsPerPage = 30;
        }

        $paginator = $table->getPaginatedCourseRecords(true,null,true,$filter,$group,$sort);
        $paginator->setCurrentPageNumber((int) (empty($params['page'])? 1 : $params['page']));
        $paginator->setItemCountPerPage($rowsPerPage);
        $total = $table->getTotalCourseRecords(true,null,true,$filter,$group,$sort);

        $output = [
            'current_page'=>(int) (empty($params['page'])? 1 : $params['page']),
            'rows_per_page'=> $rowsPerPage,
            'total' => $total
        ];

        foreach($paginator as $row){
            $data = Session::find($row->session_id)->toArray();
            if(isset($params['currency_id'])){
                $data['amount'] = price($data['amount'],$params['currency_id'],true);
            }

            $output['records'][]=$data;

        }

        return jsonResponse($output);
    }

    private function getResumeData($id){
        $sessionTable = new SessionTable();
        $sessionLessonTable = new SessionLessonTable();
        $sessionLessonAccountTable = new SessionLessonAccountTable();
        $studentSessionTable = new StudentSessionTable();
        $sessionInstructorTable = new SessionInstructorTable();
        $studentLectureTable = new StudentLectureTable();
        $logTable = new StudentSessionLogTable();
        $enrolled = false;
        $resumeData = null;

        $student = $this->getApiStudent();
        $resume = false;
        if($student) {
            $studentId = $student->student_id;
            if ($studentSessionTable->enrolled($studentId, $id)) {
                $enrolled = true;
                //check if student has started lecture
                if($studentLectureTable->hasLecture($studentId,$id)){
                    $lecture = $studentLectureTable->getLecture($studentId,$id);
                    if($lecture->sort_order == 1){

                        $resumeData = [
                            'type'=>'class',
                            'class_id'=> $lecture->lesson_id,
                            'session_id' => $id
                        ];
                    }
                    else{
                        $resumeData = [
                            'type'=>'lecture',
                            'class_id'=> $lecture->lesson_id,
                            'lecture_id'=>$lecture->lecture_id,
                            'session_id'=>$id
                        ];
                    }
                    $resume = true;
                }
                else{

                    //  $resumeLink = $this->url()->fromRoute('application/default', ['controller' => 'course', 'action' => 'intro','id'=>$id]);
                    $resumeData = [
                        'type'=>'course',
                        'session_id'=> $id,

                    ];
                }

            }

        }
        return $resumeData;
    }

    public function getSession(Request $request,Response $response,$args){


        $id = $args['id'];
        $sessionTable = new SessionTable();
        $sessionLessonTable = new SessionLessonTable();
        $sessionLessonAccountTable = new SessionLessonAccountTable();
        $studentSessionTable = new StudentSessionTable();
        $sessionInstructorTable = new SessionInstructorTable();
        $studentLectureTable = new StudentLectureTable();
        $logTable = new StudentSessionLogTable();
        $enrolled = false;
        $resumeData = null;

        $student = $this->getApiStudent();
        $resume = false;
        if($student) {
            $studentId = $student->student_id;
            if ($studentSessionTable->enrolled($studentId, $id)) {
                $enrolled = true;
                //check if student has started lecture
                if($studentLectureTable->hasLecture($studentId,$id)){
                    $lecture = $studentLectureTable->getLecture($studentId,$id);
                    if($lecture->sort_order == 1){
                        
                        $resumeData = [
                            'type'=>'class',
                            'class_id'=> $lecture->lesson_id,
                            'session_id' => $id
                        ];
                    }
                    else{
                       $resumeData = [
                            'type'=>'lecture',
                            'class_id'=> $lecture->lesson_id,
                            'lecture_id'=>$lecture->lecture_id,
                            'session_id'=>$id
                        ];
                    }
                    $resume = true;
                }
                else{

                  //  $resumeLink = $this->url()->fromRoute('application/default', ['controller' => 'course', 'action' => 'intro','id'=>$id]);
                    $resumeData = [
                        'type'=>'course',
                        'session_id'=> $id,
                        
                    ];
                }

            }

        }
        else{
            $studentId = 0;
        }
 
        $downloadSessionTable = new DownloadSessionTable();

        $row = $sessionTable->getRecord($id);
        $rowset = $sessionLessonTable->getSessionRecords($id);

        $classes = $rowset->toArray();
        foreach($classes as $key=>$value){
            $classes[$key]['lesson_date'] = (empty( $classes[$key]['lesson_date']))? '':date('d M Y',$classes[$key]['lesson_date']);
        }

        //ensure it is an online course


        //get instructors
        $instructors = $sessionInstructorTable->getSessionRecords($id);

        //get downloads
        $downloads = $downloadSessionTable->getSessionRecords($id);

        //check if student has started course
        //get session tests
        $sessionTestTable  = new SessionTestTable();
        $tests = $sessionTestTable->getSessionRecords($id);



        $output = [
            'details'=>$row,
            'classes'=>$classes,
            'session_id'=>$id,
            'studentId'=>$studentId,
            'instructors' => $instructors->toArray(),
            'downloads'=>$downloads->toArray(),
            'resumeData'=>$resumeData,
            'enrolled'=>$enrolled,
            'tests'=>$tests->toArray(),
            'resume'=> $resume
        ];

        return jsonResponse($output);
    }

    public function getClass(Request $request,Response $response,$args){
        //validate
        $params = $request->getQueryParams();
        $valid = $this->validate($params,[
           'course_id'=>'required'
        ]);

        if(!$valid){
            return jsonResponse(['error'=>$this->getValidationErrorMsg(),'status'=>false]);
        }

        
        $classId = $args['id'];
        $sessionId = $params['course_id'];

        $this->verifyClass($classId,$sessionId);

        $student = $this->getApiStudent();


        $lessonTable = new LessonTable();
        $lesson = $lessonTable->getRecord($classId);

        //check if the enforce order flag is selected


        //check if the class is opened yet



        //get list of lectures
        $lectureTable = new LectureTable();
        $lectures = $lectureTable->getRecordsOrdered($classId);
        $lectures->buffer();
        $lecturePageTable = new LecturePageTable();
        $downloadsTable = new LessonFileTable();
        $downloads = $downloadsTable->getDownloadRecords($classId);
        $downloads->buffer();

        $sessionLessonTable = new SessionLessonTable();

        //get previous class link
        $previousClass = $sessionLessonTable->getPreviousLessonInSession($sessionId,$classId,'c');
        if($previousClass){
            $previous = $previousClass->lesson_id;

        }
        else{
            $previous = false;
        }

        if($lectureTable->getTotalLectures($classId)>0){
            $nextRow = $lectures->current();
            $next = $nextRow->lecture_id;

        }
        else{
            $next = false;
        }

        $lectureArray= $lectures->toArray();

        foreach($lectureArray as $key=>$value){
            $lectureId = $lectureArray[$key]['lecture_id'];
            $contents= Lecture::find($lectureId)->lecturePages()->orderBy('lecture_id')->get();

            $data = $contents->toArray();

            foreach($data as $key2=>$value2){

                unset($data[$key2]['content']);
                unset($data[$key2]['audio_code']);
            }

            $lectureArray[$key]['contents'] = $data;

        }


        $output= [
            'class_name'=>$lesson->lesson_name,
            'lectures'=>$lectureArray,
            'class_details'=>$lesson,
            'downloads'=>$downloads->toArray(),
            'previous'=>$previous,
            'next'=>$next,
            'sessionId'=>$sessionId,
            'status'=>true

        ];


        return jsonResponse($output);

    }

    public function getLecture(Request $request,Response $response,$args){

        $params = $request->getQueryParams();
        $valid = $this->validate($params,[
            'course_id'=>'required'
        ]);

        if(!$valid){
            return jsonResponse(['error'=>$this->getValidationErrorMsg(),'status'=>false]);
        }

        $lectureId = $args['id'];
        $sessionId = $params['course_id'];
 
        $this->checkLectureOrder($lectureId,$sessionId);

        $studentVideoTable = new StudentVideoTable();
        $sessionLessonTable = new SessionLessonTable();
        $lectureTable = new LectureTable();
        $studentLectureTable = new StudentLectureTable();
        $lecture = $lectureTable->getRecord($lectureId);
        $this->verifyClass($lecture->lesson_id,$sessionId);
        $studentLectureTable->logLecture($this->getApiStudent()->student_id,$sessionId,$lectureId);

        $lecturePageTable = new LecturePageTable();
        $pages = $lecturePageTable->getRecordsOrdered($lectureId);
        $pages->buffer();

        $fileTable = new LectureFileTable();
        $downloads = $fileTable->getDownloadRecords($lectureId);
        $downloads->buffer();
        //get previous and next links
        $previous = $lectureTable->getPreviousLecture($lectureId);
        if($previous){
            $previous= Lecture::find($previous->lecture_id)->toArray();
        }


        $next = $lectureTable->getNextLecture($lectureId);
        if($next){
            $next = Lecture::find($next->lecture_id)->toArray();
        }


        $previousLesson = $sessionLessonTable->getPreviousLessonInSession($sessionId,$lecture->lesson_id,'c');


        $sessionInstructorTable = new SessionInstructorTable();

        $instructors = $sessionInstructorTable->getSessionRecords($sessionId);




        $discussionTable = new DiscussionTable();
        $discussions = $discussionTable->getPaginatedRecordsForStudent(false,$this->getApiStudent()->student_id,$sessionId,$lectureId);

        //get all lectures for this lecture's class
        $lectures = $lectureTable->getRecordsOrdered($lecture->lesson_id);

        $lectureArray= $lectures->toArray();

        foreach($lectureArray as $key=>$value){
            $lectureId = $lectureArray[$key]['lecture_id'];
            $contents= Lecture::find($lectureId)->lecturePages()->orderBy('lecture_id')->get();

            $data = $contents->toArray();

            foreach($data as $key2=>$value2){

                unset($data[$key2]['content']);
                unset($data[$key2]['audio_code']);
            }

            $lectureArray[$key]['contents'] = $data;

        }

        $pageArray = $pages->toArray();
        foreach($pageArray as $key=>$value){
            if($value['type']=='c'){
                $pageArray[$key]['content'] = htmlentities($pageArray[$key]['content']);
            }
            elseif($value['type']=='v'){
                preg_match('/src="([^"]+)"/', $pageArray[$key]['content'], $match);
                $url = $match[1];
                $pageArray[$key]['content'] = $url;
            }
            elseif($value['type']=='q'){
                $pageArray[$key]['content'] = json_decode($pageArray[$key]['content']);
            }
            elseif($value['type']=='l' && defined('USER_ID')){

                //give student access to video
                $studentVideoTable->addVideoForStudent($this->getApiStudentId(),$pageArray[$key]['content']);

                $pageArray[$key]['content'] = Video::find($pageArray[$key]['content']);
                if($pageArray[$key]['content']){
                    $pageArray[$key]['poster'] = "uservideo/".USER_ID."/".$pageArray[$key]['content']->video_id.".jpg";
                }

            }

            if(!empty($value['audio_code'])){
                preg_match('/src="([^"]+)"/', $pageArray[$key]['audio_code'], $match);
                $url = $match[1];
                $pageArray[$key]['audio_code'] = $url;
            }
        }


        $sessionEntity = Session::find($sessionId);
        $output = [
            'lecture_name'=>$lecture->lecture_title,
            'pages'=>$pageArray,
            'lecture'=>$lecture,
            'downloads'=>$downloads->toArray(),
            'previous'=>$previous,
            'next'=>$next,
            'instructors'=>$instructors->toArray(),
            'discussions'=>$discussions->toArray(),
            'totalPages'=>$pages->count(),
            'sessionId'=>$sessionId,
            'previousLesson'=>$previousLesson,
            'lectures'=>$lectureArray,
            'session'=>$sessionEntity,
            'status'=>true
        ];





        //check if there is a video in content
/*        $lecture= Lecture::find($lectureId);
        foreach ($lecture->lecturePages as $page){
            if($page->type =='l'){
                $videoId = intval($page->content);
                $video = Video::find($videoId);
                if($video){
                    $studentVideoTable = new StudentVideoTable();
                    $studentVideoTable->addVideoForStudent($this->getApiStudent()->student_id,$videoId);
                }
            }
        }*/

       return jsonResponse($output);

    }


    public function getVideo(Request $request,Response $response,$args){

        return jsonResponse(['error'=>'NOT IMPLEMENTED','status'=>false]);




    }



    public function  getServiceManager(){
        return $GLOBALS['serviceManager'];
    }

    private function checkLectureOrder($lectureId,$sessionId){

        
        $lectureTable= new LectureTable();
        $sessionLogTable = new StudentSessionLogTable();
        $lectureRow = $lectureTable->getRecord($lectureId);
        $lessonTable = new LessonTable();
        $classId = $lectureRow->lesson_id;
        $lesson = $lessonTable->getRecord($classId);
        if(!empty($lesson->enforce_lecture_order) && $lectureRow->sort_order > 1){

            if($sessionLogTable->hasAttendance($this->getApiStudent()->student_id,$sessionId,$lectureId)){
                return true;
            }
            //get previous class
            $previousLecture = $lectureTable->getPreviousLecture($lectureId);
            if(!$sessionLogTable->hasAttendance($this->getApiStudent()->student_id,$sessionId,$previousLecture->lecture_id)){
                //get the last lecture student attended
                $lectures = $lectureTable->getRecordsOrdered($classId);
                //getStudentLog
                $nextLecture = null;
                foreach($lectures as $lecture){

                    if(!$sessionLogTable->hasAttendance($this->getApiStudent()->student_id,$sessionId,$lecture->lecture_id)){

                        $nextLecture = $lecture->lecture_id;
                        break;
                    }

                }//end lessons loop

                return jsonResponse([
                   'status'=>false,
                    'error'=>'You are required to complete these lectures in the right order',
                    'next'=>$nextLecture
                ]);

            }

        }

    }

    private function verifyClass($id,$session){
//        $sessionContainer = new Container('course');
        $sessionLessonTable = new SessionLessonTable();
        $this->validateEnrollment($session);
        $this->checkOrder($id,$session);

        //check previous class and see if there is any outstanding assignment
        $previousClass = $sessionLessonTable->getPreviousLessonInSession($session,$id,'c');
        if($previousClass){

            //get assignments
            $assignementTable = new AssignmentTable();
            $assignments = $assignementTable->getSessionLessonAssignments($session,$previousClass->lesson_id);
            $assignments->buffer();
            $assignmentSubmissionTable = new AssignmentSubmissionTable();
            $total = $assignments->count();
            $firstAssignment = false;
            if(!empty($total)){
                //assignments exist
//                $sessionContainer->url = selfURL();

                //loop through assignments and verify student has submission for each
                foreach($assignments as $assignment){


                    if(!$assignmentSubmissionTable->hasSubmission($this->getApiStudent()->student_id,$assignment->assignment_id)){
                        if($firstAssignment==false){
                            $firstAssignment = $assignment;
                        }

                        $subject = 'You have Homework for '.$previousClass->lesson_name;
                        $message= 'You are required to complete the homework \''.$assignment->title.'\' for the \' '.$previousClass->lesson_name.' \' class';

                        $this->notifyStudent($this->getApiStudent()->student_id,$subject,$message);

                    }
                }

                if($firstAssignment){
                    return jsonResponse([
                       'status'=>false,
                        'error'=>'You are required to complete the homework \''.$firstAssignment->title.'\' for the \' '.$previousClass->lesson_name.' \' class.',
                        'path'=>'submit-assignment',
                        'param'=>$firstAssignment->assignment_id
                    ]);
                    
                    //redirect to assignment page
                  /*  $this->flashMessenger()->addMessage();

                    return $this->redirect()->toRoute('application/submit-assignment',['id'=>$firstAssignment->assignment_id]);*/

                }


            }

        }

        //check that class is opened
        $lessonRow = $sessionLessonTable->getSessionLessonRecord($session,$id);
        if(!empty($lessonRow->lesson_date) && $lessonRow->lesson_date > time()){
            /*      $this->flashMessenger()->addMessage('The class "'.$lessonRow->lesson_name.'" is scheduled to start on '.date('d/M/Y',$lessonRow->lesson_date));
                  $this->redirect()->toRoute('course-details',['id'=>$session]);*/

            $lesson = Lesson::find($lessonRow->lesson_id); 
             return jsonResponse([
                'status'=>false,
                'error'=>'The class "'.$lesson->lesson_name.'" is scheduled to start on '.date('d/M/Y',$lessonRow->lesson_date),
                'path'=>'course-intro',
                'param'=>$session
            ]);
        }


        if($sessionLessonTable->lessonExists($session,$id)){
            return true;
        }
        else{
            exit(jsonResponse(['Invalid record']));
        }

    }

    public function getId(){
        return $this->getApiStudent()->student_id;
    }

    private function validateEnrollment($sessionId){

        $studentSessionTable = new StudentSessionTable();
        if(!$studentSessionTable->enrolled($this->getApiStudent()->student_id,$sessionId)){
            return jsonResponse([
                'status'=>false,
                'error'=>'You are not enrolled into this course'
            ]);
            
        }
 
    }

    private function checkOrder($classId,$sessionId){
        $sessionTable= new SessionTable();
        $session = $sessionTable->getRecord($sessionId);
        $sessionLessonTable = new SessionLessonTable();
        $lesson = $sessionLessonTable->getSessionLessonRecord($sessionId,$classId);
        if(!empty($session->enforce_order) && $lesson->sort_order > 1){

            $attendanceTable = new AttendanceTable();
            if($attendanceTable->hasAttendance($this->getApiStudent()->student_id,$classId,$sessionId)){
                return true;
            }
            //get previous class
            $previousClass = $sessionLessonTable->getPreviousLessonInSession($sessionId,$classId);
            if(!$attendanceTable->hasAttendance($this->getApiStudent()->student_id,$previousClass->lesson_id,$sessionId)){
                //get the last class student attended
                $lessons = $sessionLessonTable->getSessionRecords($sessionId);

                //getStudentLog

                $nextLesson = null;
                foreach($lessons as $lesson){

                    if(!$attendanceTable->hasAttendance($this->getApiStudent()->student_id,$lesson->lesson_id,$sessionId)){

                        $nextLesson = $lesson->lesson_id;
                        break;
                    }

                }
                jsonResponse(['error'=>'You are required to complete these classes in the right order','status'=>false,'param'=>$nextLesson,'path'=>'class']);
            
               
            }

        }

    }

    public function  studentCourses(){

            $student = $this->getApiStudent();
            $studentId = $student->student_id ;
            $studentSessionTable = new StudentSessionTable();
            $attendanceTable = new AttendanceTable();
            $paginator = $studentSessionTable->getStudentRecords(false,$studentId);
//            $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
//            $paginator->setItemCountPerPage(10);

            $total = $studentSessionTable->getTotalForStudent($studentId);
            $records = $paginator->toArray();

            foreach($records as $key=>$value){
                $records[$key]['resume']= $this->getResumeData($value['session_id']);
            }


            $output = [
                'sessions'=>$records,
                'total'=>$total
            ];

        return jsonResponse($output);

    }

    public function getIntro(Request $request,Response $response,$args){
        $id = $args['id'];
        $this->validateEnrollment($id);
        $sessionTable = new SessionTable();
        $sessionLessonTable = new SessionLessonTable();
        $lectureTable = new LectureTable();
        $row = $sessionTable->getRecord($id);

        $downloadSessionTable = new DownloadSessionTable();
        $downloads = $downloadSessionTable->getSessionRecords($id);
        $instructorsTable = new SessionInstructorTable();

        //get class list
        $classes = $sessionLessonTable->getSessionRecords($id,'c');
        $classes->buffer();

        //get discussions for course
        $discussionTable = new DiscussionTable();
        $discussions = $discussionTable->getPaginatedRecordsForStudent(false,$this->getApiStudent()->student_id,$id);

        $instructors = $instructorsTable->getSessionRecords($id);
        $instructors->buffer();
        $options = [];
        $options['admins'] = 'Administrators';
        foreach($instructors as $row2){

            $options[$row2->account_id]= $row2->first_name.' '.$row2->last_name;


        }




        //get first class
        $firstClass = $classes->current();


        //calculate progress of attendance
        $attendanceTable = new AttendanceTable();
        $totalClasses = $sessionLessonTable->getSessionRecords($id)->count();
        //get tottal attended
        $attended = $attendanceTable->getTotalDistinctForStudentInSession($this->getApiStudent()->student_id,$id);

        $percentage= ($attended/$totalClasses) * 100;

        $attendanceRecords = $attendanceTable->getAttendedRecords($this->getApiStudent()->student_id,$id);

       // $classesArray = $classes->toArray();

        $classesArray=[];
        foreach($classes as $class){

            $data = Lesson::find($class->lesson_id)->toArray();
            $lectures = $lectureTable->getPaginatedRecords(false,$class->lesson_id);
            $data['lectures'] = $lectures->toArray();
            $classesArray[] = $data;


        }

        //get classes not attended
        $pendingClasses = [];
        foreach($classes as $class){
            if(!$attendanceTable->hasAttendance($this->getApiStudent()->student_id,$class->lesson_id,$id)){
                $pendingClasses[] = Lesson::find($class->lesson_id)->toArray();
            }
        }


        $output= [
            'session_name'=>$row->session_name,
          'classes'=>$classesArray,
            'sessionRow'=>$row,
            'downloads'=>$downloads->toArray(),
            'discussions'=> $discussions->toArray(),
            'sessionId'=>$id,
            'instructors'=>$instructors->toArray(),
              'percentage'=>$percentage,
            'studentId'=> $this->getApiStudent()->student_id,
            'sessionId'=>$id,
            'classesAttended'=>$attendanceRecords->toArray(),
            'pendingClasses'=>$pendingClasses
        ];




        return jsonResponse($output);
    }




    public function classFile(Request $request,Response $response,$args){
        set_time_limit(86400);
        $id = $args['id'];
        $params = $request->getQueryParams();
        $sessionId = $params['course_id'];
        $table = new LessonFileTable();
        $row = $table->getRecord($id);
        $this->verifyClass($row->lesson_id,$sessionId);

        $path = 'public/usermedia/'.$row->path;
        header('Content-type: '.getFileMimeType($path));
        header('Content-Disposition: attachment; filename="'.basename($path).'"');
        readfile($path);
        exit();
    }


    public function lectureFile(Request $request,Response $response,$args){
        set_time_limit(86400);
        $id = $args['id'];
        $params = $request->getQueryParams();
        $sessionId = $params['course_id'];
        $table = new LectureFileTable();
        $row = $table->getRecord($id);

        $fileRow = LectureFile::get(id);
        $this->verifyClass($fileRow->lecture->lesson_id,$sessionId);

        $path = 'public/usermedia/'.$row->path;
        header('Content-type: '.getFileMimeType($path));
        header('Content-Disposition: attachment; filename="'.basename($path).'"');
        readfile($path);
        exit();
    }


    public function loglecture(Request $request,Response $response,$args){


        $data = $request->getParsedBody();

        $rules = [
            'lecture_id'=>'required',
            'session_id'=>'required', 
        ];
        $isValid = $this->validate($data,$rules);

        if(!$isValid){
            return jsonResponse(['status'=>false,'msg'=>$this->getValidationErrors()]);
        }


        $sessionLogTable = new StudentSessionLogTable();
        $studentTestTable = new StudentTestTable();
        $sessionLessonTable = new SessionLessonTable();
        $testTable = new TestTable();
        $studentLectureTable = new StudentLectureTable(); 
       

            $lecture = $data['lecture_id'];
            $session = $data['session_id'];

            $lectureTable = new LectureTable();
            $lectureRow = $lectureTable->getRecord($lecture);
            $lessonTable = new LessonTable();
            $this->verifyClass($lectureRow->lesson_id,$session);

            $logId = $sessionLogTable->addRecord([
                'student_id'=>$this->getApiStudent()->student_id,
                'session_id'=>$session,
                'lecture_id'=>$lecture,
                'log_date'=>time()
            ]);

            //check if there is another lecture and redirect to it
            $next = $lectureTable->getNextLecture($lecture);
            if($next){
                return jsonResponse([
                   'status'=>true,
                    'redirect_type'=>'lecture',
                    'lecture_id'=>$next->lecture_id,
                    'message'=>false
                ]);
               
            }
            else{
                //check for outstanding lectures
                $allLectures = $lectureTable->getLectureRecords($lectureRow->lesson_id);
                foreach($allLectures as $row){

                    if(!$sessionLogTable->hasAttendance($this->getApiStudent()->student_id,$session,$row->lecture_id)){

                        return jsonResponse([
                            'status'=>true,
                            'redirect_type'=>'lecture',
                            'lecture_id'=>$row->lecture_id,
                            'message'=>'You have outstanding lectures. Please complete all lectures in this class'
                        ]);
                  
                    }

                }



                //check if class has test
                $lessonRow = $lessonTable->getRecord($lectureRow->lesson_id);
                if(!empty($lessonRow->test_required) && !empty($lessonRow->test_id) && $testTable->recordExists($lessonRow->test_id) && !$studentTestTable->passedTest($this->getApiStudent()->student_id,$lessonRow->test_id)){

                   /* $container->testInfo = serialize([$lessonRow->test_id=>[
                        'lesson_id'=>  $lectureRow->lesson_id,
                        'session_id'=>$session,
                        'lesson_name'=>$lessonRow->lesson_name
                    ]]);*/


                    $nextClass = $sessionLessonTable->getNextLessonInSession($session,$lectureRow->lesson_id,'c');


                    return jsonResponse([
                       'status'=>true,
                        'redirect_type'=>'test',
                        'test_id'=>$lessonRow->test_id,
                        'lesson_id'=>  $lectureRow->lesson_id,
                        'session_id'=>$session,
                        'lesson_name'=>$lessonRow->lesson_name,
                        'message'=>'You are required to take this test in order to complete the \''.$lessonRow->lesson_name.'\' class'
                    ]);
                }
                else{

                    //log attendance for this class
                    $attendanceTable = new AttendanceTable();
                    $attendanceTable->setAttendance([
                        'student_id'=>$this->getApiStudent()->student_id,
                        'session_id'=>$session,
                        'lesson_id'=>$lectureRow->lesson_id
                    ]);

                    //get next class
                    $nextClass = $sessionLessonTable->getNextLessonInSession($session,$lectureRow->lesson_id,'c');
                    if($nextClass){
                        //forward to the next class
                         return jsonResponse([
                           'status'=>true,
                            'redirect_type'=>'class',
                            'class_id'=>$nextClass->lesson_id,
                             'message'=>false
                        ]);

                    }
                    else{
                        //classes are over
                        $studentLectureTable->clearLecture($this->getApiStudent()->student_id,$session);
                        $studentSessionTable = new StudentSessionTable();
                        $studentSessionTable->markCompleted($this->getApiStudent()->student_id,$session);

                        return jsonResponse([
                           'status'=>true,
                            'redirect_type'=>'course',
                            'session_id'=>$session,
                            'message'=>'Congratulations! You have successfully completed this course'
                        ]);
                    }

                }

            }

        
        
    }


    public function bookmarks(Request $request,Response $response,$args){

        $rowset = Bookmark::where('student_id',$this->getApiStudentId())->paginate(30);

        $data = $rowset->toArray();

        foreach($data['data'] as $key=>$value){

            $data['data'][$key]['session_name'] = Session::find($value['session_id'])->session_name;
            $lecturePage = LecturePage::find($value['lecture_page_id']);
            $data['data'][$key]['lesson_name'] = $lecturePage->lecture->lesson->lesson_name;
            $data['data'][$key]['lecture'] = $lecturePage->lecture->lecture_title;
            $data['data'][$key]['lecture_page'] = $lecturePage->title;
        }

        return jsonResponse($data);
    }

    public function storeBookmark(Request $request,Response $response,$args){

        $data = $request->getParsedBody();
        $this->validateParams($data,[
           'session_id'=>'required',
            'lecture_page_id'=>'required',
        ]);
        $data['student_id'] = $this->getApiStudentId();
        $data['created_on'] = time();

        $bookMark = Bookmark::create($data);
        return jsonResponse([
            'status'=>true
        ]);

    }

    public function removeBookmark(Request $request,Response $response,$args){

        $id = $args['id'];
        $row = Bookmark::find($id);
        $this->validateApiOwner($row);

        $row->delete();
        return jsonResponse([
            'status'=>true
        ]);
    }



}