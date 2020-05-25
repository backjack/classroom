<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 11/22/2018
 * Time: 11:00 AM
 */

namespace Application\Slim\V1\Controller;
use Application\Entity\Student;
use Application\Entity\StudentTest;
use Application\Entity\Test;
use Application\Model\StudentSessionTable;
use Application\Model\StudentTestOptionTable;
use Application\Model\StudentTestTable;
use Application\Model\TestGradeTable;
use Application\Model\TestOptionTable;
use Application\Model\TestQuestionTable;
use Application\Model\TestTable;
use Intermatics\HelperTrait;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Zend\Session\Container;

class TestController extends Controller {

    use HelperTrait;

    public function tests(Request $request,Response $response,$args)
    {
        $params = $request->getQueryParams();

        // TODO Auto-generated NewsController::indexAction() default action
        $table = new TestTable();
        $testQuestionTable = new TestQuestionTable();
        $studentTestTable = new StudentTestTable();

        $paginator = $table->getStudentRecords($this->getApiStudent()->student_id);

        $currentPage = (int) (empty($params['page'])? 1 : $params['page']);
        $rowsPerPage = 30;

        $paginator->setCurrentPageNumber($currentPage);
        $paginator->setItemCountPerPage($rowsPerPage);

        $output=[];

        $output['total'] = $table->getStudentTotalRecords($this->getApiStudent()->student_id);
        $output['current_page'] = $currentPage;

        $totalPages = ceil($output['total']/$rowsPerPage);
        $output['total_pages'] = $totalPages;
        $output['records']=[];

        if($currentPage<=$totalPages){


            foreach($paginator as $value){
                $test = Test::find($value->test_id);
                $test->total_questions = $test->testQuestions()->count();

                //check if student has test
                $test->total_attempts = $test->studentTests()->where('student_id',$this->getApiStudentId())->count();

                $canTake = true;
                if(empty($test->allow_multiple) && $studentTestTable->hasTest($test->test_id,$this->getApiStudentId())){
                    $canTake = false;
                }
                $test->can_take = $canTake;


                $output['records'][]= $test;
                //
            }

        }



        return jsonResponse(
             $output
        );

    }


    public function getTest(Request $request,Response $response,$args)
    {
        

        $id = $args['id'];
        $output = [];
        $testTable = new TestTable();
        $testRow=$testTable->getRecord($id);
        $output['testRow'] = $testRow;
        $questionTable = new TestQuestionTable();
        $optionTable = new TestOptionTable();
        $studentTestTable = new StudentTestTable();
        $studentTestOptionTable = new StudentTestOptionTable();
        $studentSessionTable = new StudentSessionTable();

        if($studentTestTable->hasTest($id,$this->getApiStudent()->student_id) && empty($output['testRow']->allow_multiple)){

            return jsonResponse([
               'status'=>false,
                'message'=>'You have already taken this test'
            ]);
        }


        if(!empty($testRow->private)){

            //get records for the student
            $rowset = $testTable->getStudentTestRecords($this->getApiStudent()->student_id,$id);
            $total = $rowset->count();


            if(empty($total)){
                return jsonResponse([
                    'status'=>false,
                    'message'=>'You do not have permission to take this test'
                ]);
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

                return jsonResponse([
                    'status'=>false,
                    'message'=>'This test has closed or is yet to open'
                ]);
            }


        }


        $rowset = $questionTable->getPaginatedRecords(false,$id);
        $rowset->buffer();
        $questions = [];
        $correct = 0;
        $totalQuestions = $rowset->count();
/*        $counter=0;
        foreach($rowset as $row)
        {
            $questions[$counter]['question'] = $row;
            $questions[$counter]['options'] = $optionTable->getOptionRecords($row->test_question_id)->toArray();
            foreach( $questions[$counter]['options'] as $key=>$value){
                unset($questions[$counter]['options'][$key]['is_correct']);
            }

            $counter++;
        }
*/

        $output['totalQuestions'] = $totalQuestions;
        $testRow->total_questions = $totalQuestions;

        return jsonResponse($testRow);
    }

    public function createStudentTest(Request $request,Response $response,$args)
    {
        $data = $request->getParsedBody();

        $rules = [
            'test_id'=>'required', 
        ];
        $isValid = $this->validate($data,$rules);

        if(!$isValid){
            return jsonResponse(['status'=>false,'msg'=>$this->getValidationErrors()]);
        }
        
        $studentTestTable = new StudentTestTable();


        
        $id = $data['test_id'];

        $test = Test::find($id);

        if(empty($test->allow_multiple) && $studentTestTable->hasTest($test->test_id,$this->getApiStudentId())){
            return jsonResponse([
                'status'=>false,
                'msg'=>'You have already taken this test'
            ]);

        }
        
        $studentTestId = $studentTestTable->addRecord([
            'student_id'=>$this->getApiStudent()->student_id,
            'test_id'=>$id,
            'created_on'=>time(),
            'score'=>0
        ]);

        $output = ['id'=>$studentTestId,'status'=>true];

        //get test questions
        $questionTable = new TestQuestionTable();
        $optionTable = new TestOptionTable();

        $rowset = $questionTable->getPaginatedRecords(false,$id);
        $rowset->buffer();
        $questions = [];
        $correct = 0;
        $totalQuestions = $rowset->count();
        $counter=0;
        foreach($rowset as $row)
        {
            $questions[$counter]['question'] = $row;
            $questions[$counter]['options'] = $optionTable->getOptionRecords($row->test_question_id)->toArray();
            foreach( $questions[$counter]['options'] as $key=>$value){
                unset($questions[$counter]['options'][$key]['is_correct']);
            }

            $counter++;
        }


        $output['total_questions'] = $totalQuestions;
        $output['questions'] = $questions;



        return jsonResponse($output);
         
    }
    
    public function updateStudentTest(Request $request,Response $response,$args){

        $data = $request->getParsedBody();



        $studentTestId = $args['id'];
        $testId = StudentTest::find($studentTestId)->test_id;
        $output = [];
        $testTable = new TestTable();
        $test = $testTable->getRecord($testId);

        $output['testRow'] = $test;
        //check if student has taken test before



        $questionTable = new TestQuestionTable();
        $optionTable = new TestOptionTable();
        $studentTestTable = new StudentTestTable();
        $studentTestOptionTable = new StudentTestOptionTable();




        $rowset = $questionTable->getPaginatedRecords(false,$testId);
        $rowset->buffer();
        $questions = [];
        $correct = 0;
        $totalQuestions = $rowset->count();
        foreach($rowset as $row)
        {
            $questions[$row->test_question_id]['question'] = $row;
            $questions[$row->test_question_id]['options'] = $optionTable->getOptionRecords($row->test_question_id);
        }


        $row = $studentTestTable->getRecord($studentTestId);
        $this->validateApiOwner($row);

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
        $score = round($score,2);
        //update
        $studentTestTable->update(['score'=>$score],$studentTestId);

        $studentTestRecord = StudentTest::find($studentTestId)->toArray();
        $studentTestRecord['status'] = true;



        return jsonResponse($studentTestRecord);

    }

    public function getStudentTest(Request $request,Response $response,$args){

        $id = $args['id'];

        $row = StudentTest::find($id);

        $data = [];
        $data['details'] = $row->toArray();

        $data['test'] = $row->test->toArray();

        return jsonResponse($data);

    }


    public function studentTests(Request $request,Response $response,$args){

        $params = $request->getQueryParams();

       $isValid= $this->validate($params,[
           'test_id'=>'required'
        ]);

        if(!$isValid){

            return jsonResponse(['status'=>false,'msg'=>$this->getValidationErrors()]);

        }

        $id =  $params['test_id'];
        $test = Test::findOrFail($id);
        if(empty($test->show_result)){

            return jsonResponse(['status'=>false,'msg'=>'You are not allowed to view results for this test']);
        }

        $rowsPerPage= 30;
        //get test
        $studentId = $this->getApiStudentId();
        $student = Student::find($studentId);
        $rowset = $student->studentTests()->orderBy('created_on','desc')->where('test_id',$id)->paginate($rowsPerPage);

        $gradeTable = new TestGradeTable();
        $output = [];
        $output['rows_per_page'] = $rowsPerPage;
        $output['current_page'] = empty($params['page']) ? 1:$params['page'];
        $output['total'] = $student->studentTests()->count();
        foreach($rowset as $row){
            $data = $row->toArray();
            $data['grade'] = $gradeTable->getGrade($row->score);
            $output['records'][] = $data;
        }

        return jsonResponse($output);
    }

}