<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 5/24/2018
 * Time: 3:44 PM
 */

namespace Admin\Controller;


use Application\Controller\AbstractController;
use Application\Entity\AssignmentSubmission;
use Application\Entity\Lesson;
use Application\Entity\Session;
use Application\Entity\Student;
use Application\Entity\StudentTest;
use Application\Entity\Test;
use Application\Model\AttendanceTable;
use Application\Model\SessionCategoryTable;
use Application\Model\SessionLessonTable;
use Application\Model\SessionTable;
use Application\Model\StudentSessionTable;
use Application\Model\TestGradeTable;
use Dompdf\Dompdf;
use Intermatics\HelperTrait;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\View\Model\ViewModel;

class ReportController extends AbstractController {
    use HelperTrait;

    public function indexAction(){

        $table = new SessionTable($this->getServiceLocator());
        $attendanceTable = new AttendanceTable($this->getServiceLocator());
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());

        $filter = $this->params()->fromQuery('filter', null);


        if (empty($filter)) {
            $filter=null;
        }

        $group = $this->params()->fromQuery('group', null);
        if (empty($group)) {
            $group=null;
        }

        $sort = $this->params()->fromQuery('sort', null);
        if (empty($sort)) {
            $sort=null;
        }

        $type = $this->params()->fromQuery('type', null);
        if (empty($type)) {
            $type=null;
        }

        $text = new Text('filter');
        $text->setAttribute('class','form-control');
        $text->setAttribute('placeholder',__('Search'));
        $text->setValue($filter);

        $select = new Select('group');
        $select->setAttribute('class','form-control select2');
        $select->setEmptyOption('--'.__('Select Category').'--');

        $sortSelect = new Select('sort');
        $sortSelect->setAttribute('class','form-control');
        //$sortSelect->setAttribute('style','max-width:100px');
        $sortSelect->setValueOptions([
            'recent'=>__('Recently Added'),
            'asc'=>__('Alphabetical (Ascending)'),
            'desc'=>__('Alphabetical (Descending)'),
            'date'=>__('Start Date'),
            'priceAsc' =>__('Price (Lowest to Highest)'),
            'priceDesc' => __('Price (Highest to Lowest)')
        ]);
        $sortSelect->setEmptyOption('--'.__('Sort').'--');
        $sortSelect->setValue($sort);

        $typeSelect = new Select('type');
        $typeSelect->setAttribute('class','form-control');
        //$typeSelect->setAttribute('style','max-width:100px');
        $typeSelect->setValueOptions([
            's'=>__('Training Session'),
            'c'=>__('Online Course'),
            'b'=>__('training-online'),
        ]);
        $typeSelect->setEmptyOption('--'.__('Type').'--');
        $typeSelect->setValue($type);

        $groupTable = new SessionCategoryTable($this->getServiceLocator());
        $groupRowset = $groupTable->getLimitedRecords(1000);
        $options =[];

        foreach($groupRowset as $row){
            $options[$row->session_category_id] = $row->category_name;
        }
        $select->setValueOptions($options);
        $select->setValue($group);

        $paginator = $table->getPaginatedRecords(true,null,null,$filter,$group,$sort,$type);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        
        return new ViewModel(array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Reports'),
            'attendanceTable'=>$attendanceTable,
            'studentSessionTable'=>$studentSessionTable,
            'filter'=>$filter,
            'group'=>$group,
            'text'=>$text,
            'select'=>$select,
            'sortSelect'=>$sortSelect,
            'sort'=>$sort,
            'typeSelect'=>$typeSelect,
            'type'=>$type
        ));

    }

    public function classesAction(){
        $id = $this->params('id');
        $session = Session::findorFail($id);

        $this->data['pageTitle']=__('Class Report').': '.$session->session_name;
        $this->data['session'] = $session;
        $this->data['attendanceTable'] = new AttendanceTable();
        $this->data['sessionLessonTable'] = new SessionLessonTable();
        $this->data['id'] = $id;
        return $this->bladeView('admin.report.classes',$this->data);

    }

    public function studentsAction(){
        $id = $this->params('id');
        $attendanceTable = new AttendanceTable();
        $this->data['rowset'] = $attendanceTable->getStudentSessionReportRecords($id);
        $this->data['id']=$id;
        $session= Session::findOrFail($id);
        $this->data['pageTitle']=__('Student Report').': '.$session->session_name;
        $this->data['session'] = $session;
        $this->data['attendanceTable'] = $attendanceTable;

        $totalLessons = $session->sessionLessons()->count();
        if(empty($totalLessons)){
            $totalLessons= 1;
        }
        $this->data['totalSessionLessons'] = $totalLessons;



        $this->data['allTests'] = $this->getSessionTests($id);
        $this->data['controller'] = $this;
        $this->data['testGradeTable'] = new TestGradeTable();

        return $this->bladeView('admin.report.students',$this->data);
    }

    public function testsAction(){
        $id = $this->params('id');
        $this->data['tests'] = $this->getSessionTestsObjects($id);
        $this->data['allTests'] = $this->getSessionTests($id);
        //get studentlist


        $this->data['controller'] = $this;
        $this->data['testGradeTable'] = new TestGradeTable();
        $this->data['pageTitle'] = __('Test Report').': '.Session::find($id)->session_name;

        $attendanceTable = new AttendanceTable();
        $this->data['rowset'] = $attendanceTable->getStudentSessionReportRecords($id);
        $this->data['session']= Session::find($id);

        return $this->bladeView('admin.report.tests',$this->data);
    }

    public function homeworkAction(){
        $id = $this->params('id');
        $session = Session::findOrFail($id);
        $this->data['session'] = $session;

        $this->data['pageTitle'] = __('Homework Report').': '.$session->session_name;
        $this->data['controller'] = $this;
        $attendanceTable = new AttendanceTable();
        $this->data['rowset'] = $attendanceTable->getStudentSessionReportRecords($id);
        $this->data['testGradeTable'] = new TestGradeTable();


        return $this->bladeView('admin.report.homework',$this->data);
    }

    public function reportcardAction(){
        $id = $this->params('id');
        $sessionId = $this->request->getQuery('sessionId');
        $this->data['tests'] = $this->getSessionTestsObjects($sessionId);
        $this->data['allTests'] = $this->getSessionTests($sessionId);
        //get studentlist


        $this->data['controller'] = $this;
        $this->data['testGradeTable'] = new TestGradeTable();
        $student = Student::find($id);
        $this->data['student'] = $student;
        $this->data['session'] = Session::find($sessionId);
        $this->data['baseUrl'] = $this->getBaseUrl();
        $html = $this->bladeHtml('admin.report.reportcard',$this->data);

        $dompdf = new Dompdf();

        $dompdf->loadHtml($html);
        $orientation = 'portrait';

        $dompdf->setPaper('A4', $orientation);
        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream(safeUrl($student->first_name.' '.$student->last_name.' report '.$this->data['session']->session_name).'.pdf');


        exit();

    }



    public function getStudentTestsStats($studentId){

        $totalTaken = 0;
        $scores = 0;


        foreach($this->data['allTests'] as $testId){
            $studentTest = StudentTest::where('student_id',$studentId)->where('test_id',$testId)->orderBy('score','desc')->first();
            if($studentTest){
                $totalTaken++;
                $scores += $studentTest->score;
            }
        }

        if(!empty($totalTaken)){
            return [
                'testsTaken'=>$totalTaken,
                'average' => ($scores/$totalTaken)
            ];
        }
        else{
            return [
                'testsTaken'=>$totalTaken,
                'average' => 0
            ];
        }


    }

    public function getStudentAssignmentStats($studentId){
        $session= $this->data['session'];
        $totalTaken = 0;
        $scores = 0;

        foreach($session->assignments as $assignment){
            $submission= AssignmentSubmission::where('student_id',$studentId)->where('assignment_id',$assignment->assignment_id)->orderBy('grade','desc')->first();
            if($submission){
                $totalTaken++;
                $scores+=$submission->grade;
            }
        }



        if(!empty($totalTaken)){
            return [
                'submissions'=>$totalTaken,
                'average' => ($scores/$totalTaken)
            ];
        }
        else{
            return [
                'submissions'=>$totalTaken,
                'average' => 0
            ];
        }


    }

    public function getStudentTotalPosts($studentId){
        $total = 0;

        foreach($this->data['session']->forumTopics as $topic){

            foreach($topic->forumPosts()->where('post_owner',$studentId)->where('post_owner_type','s')->get() as $row){
                $total++;
            }

        }
        return $total;
    }

    private function getSessionTests($sessionId){
        $session = Session::find($sessionId);
        //create list of tests for this session
        $allTests = [];
        foreach($session->sessionTests as $test){
            $allTests[$test->test_id] = $test->test_id;
        }

        foreach($session->sessionLessons as $sessionLesson){
            $lesson = Lesson::find($sessionLesson->lesson_id);

            if( $lesson && !empty($lesson->test_id) && !empty($lesson->test_required) && Test::find($lesson->test_id)){
                $allTests[$lesson->test_id] = $lesson->test_id;
            }

        }
        return $allTests;
    }

    private function getSessionTestsObjects($sessionId){
        $testIds = $this->getSessionTests($sessionId);
        $objects = [];
      foreach($testIds as $id)
      {
          $test = Test::find($id);
          if($test){
              $objects[] = $test;
          }
      }
        return $objects;
    }
}