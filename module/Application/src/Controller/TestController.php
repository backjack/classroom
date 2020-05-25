<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 2/2/2017
 * Time: 2:42 PM
 */

namespace Application\Controller;


use Application\Entity\Lesson;
use Application\Entity\Session;
use Application\Entity\Student;
use Application\Entity\StudentTest;
use Application\Entity\Test;
use Application\Model\AttendanceTable;
use Application\Model\SessionLessonTable;
use Application\Model\SessionTable;
use Application\Model\StudentSessionTable;
use Application\Model\StudentTestOptionTable;
use Application\Model\StudentTestTable;
use Application\Model\TestGradeTable;
use Application\Model\TestOptionTable;
use Application\Model\TestQuestionTable;
use Application\Model\TestTable;
use Dompdf\Dompdf;
use Intermatics\HelperTrait;
use Intermatics\UtilityFunctions;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class TestController extends AbstractController {

    use HelperTrait;
    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);
        $controller = $this;
        $events->attach('dispatch', function ($e) use ($controller) {
            $controller->layout('layout/student');
        }, 100);
    }
    public function indexAction()
    {
        // TODO Auto-generated NewsController::indexAction() default action
        $table = new TestTable($this->getServiceLocator());
        $testQuestionTable = new TestQuestionTable($this->getServiceLocator());
        $studentTestTable = new StudentTestTable($this->getServiceLocator());

        $paginator = $table->getStudentRecords($this->getId());

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        return new ViewModel(array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Tests'),
            'studentTest'=>$studentTestTable,
            'questionTable'=>$testQuestionTable
        ));

    }

    public function taketestAction()
    {
        $container = new Container('classTest');

        $id = $this->params('id');
        $output = [];
        $testTable = new TestTable($this->getServiceLocator());
        $testRow=$testTable->getRecord($id);
        $output['testRow'] = $testRow;
        $questionTable = new TestQuestionTable($this->getServiceLocator());
        $optionTable = new TestOptionTable($this->getServiceLocator());
        $studentTestTable = new StudentTestTable($this->getServiceLocator());
        $studentTestOptionTable = new StudentTestOptionTable($this->getServiceLocator());
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $output['pageTitle'] = __('Take Test').': '.$output['testRow']->name;

        if($studentTestTable->hasTest($id,$this->getId()) && empty($output['testRow']->allow_multiple)){
            $this->flashMessenger()->addMessage(__('test-taken-msg'));
            $this->redirect()->toRoute('application/test');
        }


        if(!empty($testRow->private) && !isset($container->testInfo[$id])){

            //get records for the student
            $rowset = $testTable->getStudentTestRecords($this->getId(),$id);
            $total = $rowset->count();


            if(empty($total)){
                $this->flashMessenger()->addMessage(__('no-test-permission'));
                return $this->goBack();
            }

            //now loop rowset as see if the test is opened
            $opened = false;

            foreach($rowset as $row){
                if(($row->opening_date < time() || $row->opening_date==0)){
                    $opened=true;
                }

            }

            $closed = false;

            foreach($rowset as $row){
                if($row->closing_date > time() || $row->closing_date==0){
                    $closed = true;
                }

            }

            if(!($opened && $closed)){

                $this->flashMessenger()->addMessage(__('test-closed'));
               return $this->goBack();
            }


        }


        $rowset = $questionTable->getPaginatedRecords(false,$id);
        $rowset->buffer();
        $questions = [];
        $correct = 0;
        $totalQuestions = $rowset->count();
        foreach($rowset as $row)
        {
            $questions[$row->test_question_id]['question'] = $row;
            $questions[$row->test_question_id]['options'] = $optionTable->getOptionRecords($row->test_question_id);
        }


        $output['totalQuestions'] = $totalQuestions;
        $output['questions'] = $questions;
        $output['optionTable']= $optionTable;
        if(isset($container->testInfo)){

            $testInfo = unserialize($container->testInfo);
            if(isset($testInfo[$id])){
                 $output['message'] = __('class-test',['class'=>$testInfo[$id]['lesson_name']]);
            }
        }
        return $output;
    }

    public function processtestAction()
    {
        $id = $this->params('id');
        $output = [];
        $testTable = new TestTable($this->getServiceLocator());
        $output['testRow'] = $testTable->getRecord($id);
        $questionTable = new TestQuestionTable($this->getServiceLocator());
        $optionTable = new TestOptionTable($this->getServiceLocator());
        $studentTestTable = new StudentTestTable($this->getServiceLocator());
        $studentTestOptionTable = new StudentTestOptionTable($this->getServiceLocator());


        $rowset = $questionTable->getPaginatedRecords(false,$id);
        $rowset->buffer();
        $questions = [];
        $correct = 0;
        $totalQuestions = $rowset->count();
        foreach($rowset as $row)
        {
            $questions[$row->test_question_id]['question'] = $row;
            $questions[$row->test_question_id]['options'] = $optionTable->getOptionRecords($row->test_question_id);
        }

        if($this->request->isPost())
        {
            $data = $this->request->getPost();
            $studentTestId = $data['student_test_id'];
            $row = $studentTestTable->getRecord($studentTestId);
            $this->validateOwner($row);

            foreach($rowset as  $row)
            {
                if(!empty($data['question_'.$row->test_question_id]))
                {
                    $optionId = $data['question_'.$row->test_question_id];
                    $studentTestOptionTable->addRecord([
                        'student_test_id'=>$studentTestId,
                        'test_option_id'=>$optionId
                    ]);
                    //check if option is correct
                    $optionRow = $optionTable->getRecord($optionId);
                    if($optionRow->is_correct==1){
                        $correct++;
                    }

                }
            }

            //calculate score
            $score = ($correct/$totalQuestions)  * 100;
            //update
            $studentTestTable->update(['score'=>$score],$studentTestId);
            $this->redirect()->toRoute('application/testresult',['id'=>$studentTestId]);

        }
        else{
            $this->redirect()->toRoute('application/taketest',['id'=>$id]);
        }



    }

    public function starttestAction()
    {
        $studentTestTable = new StudentTestTable($this->getServiceLocator());
        $id = $this->params('id');
        $studentTestId = $studentTestTable->addRecord([
            'student_id'=>$this->getId(),
            'test_id'=>$id,
            'created_on'=>time(),
            'score'=>0
        ]);

        $output = json_encode(['id'=>$studentTestId,'status'=>true]);
        exit($output);
    }



    public function resultAction()
    {
        $id = $this->params('id');
        $studentTestTable = new StudentTestTable($this->getServiceLocator());
        $testTable = new TestTable($this->getServiceLocator());
        $row = $studentTestTable->getRecord($id);
        $this->validateOwner($row);
        $testRow = $testTable->getRecord($row->test_id);

        $container = new Container('classTest');
        if(isset($container->testInfo)){
            $testInfo = unserialize($container->testInfo);
        }
        else{
            $testInfo= [];
        }
        if(isset($testInfo[$testRow->test_id])){
            $sessionId = $testInfo[$testRow->test_id]['session_id'];
            $lessonId = $testInfo[$testRow->test_id]['lesson_id'];
            if($row->score >= $testRow->passmark){
                //set attendance for class
                $attendanceTable = new AttendanceTable($this->getServiceLocator());
                $attendanceTable->setAttendance([
                    'student_id'=>$this->getId(),
                    'session_id'=>$sessionId,
                    'lesson_id'=>$lessonId
                ]);

                $this->flashMessenger()->addMessage(__('class-test-complete',['score'=>$row->score]));
                $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());
                $nextClass = $sessionLessonTable->getNextLessonInSession($sessionId,$lessonId,'c');
                if($nextClass){
                    //forward to the next class
                    $this->redirect()->toRoute('view-class',['sessionId'=>$sessionId,'classId'=>$nextClass->lesson_id]);
                }
                else{
                    //classes are over
                    $this->flashMessenger()->addMessage(__('course-complete-msg'));
                    $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
                    $studentSessionTable->markCompleted($this->getId(),$sessionId);
                    $this->redirect()->toRoute('course-details',['id'=>$sessionId]);
                }
            }
            else{
                $this->flashMessenger()->addMessage(__('low-test-score',['score'=>$row->score]));
                $this->redirect()->toRoute('view-class',['sessionId'=>$sessionId,'classId'=>$lessonId]);

            }

        }

        return['row'=>$row,'pageTitle'=>__('Test Result').': '.$testRow->name,'testRow'=>$testRow];
    }


    public function testresultsAction(){
        $id = $this->params('id');

        $test = Test::findOrFail($id);
        if(empty($test->show_result)){
            $this->flashMessenger()->addMessage(__('not-allowed-result'));
            return $this->goBack();
        }
        //get test
        $studentId = $this->getId();
        $student = Student::find($studentId);
        $rowset = $student->studentTests()->orderBy('created_on','desc')->where('test_id',$id)->paginate(30);


        return $this->bladeView('application.test.testresults',['pageTitle'=>__('Test Results').': '.$test->name,
            'rowset'=>$rowset,
            'test'=>$test,
            'gradeTable'=>new TestGradeTable()
        ]);

    }

    public function reportcardAction(){
        $id = $this->getId();
        $sessionId = $this->params('id');
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

    public function statementAction(){
        $id = $this->getId();
        $student = Student::findOrFail($id);
        $this->data['sessions'] = $student->studentSessions()->orderBy('enrolled_on','desc')->paginate();
        $this->data['pageTitle'] = __('Statement Of Result');
        return $this->bladeView('application.test.statement',$this->data);
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

    public function getStudentTestsStats($studentId){

        $totalTaken = 0;
        $scores = 0;



        foreach($this->data['allTests'] as $testId){
            $studentTest = StudentTest::where('student_id',$studentId)->orderBy('score','desc')->where('test_id',$testId)->first();
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

}