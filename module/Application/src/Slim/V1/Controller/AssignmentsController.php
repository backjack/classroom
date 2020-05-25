<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 11/22/2018
 * Time: 10:52 AM
 */

namespace Application\Slim\V1\Controller;


use Application\Entity\Assignment;
use Application\Entity\AssignmentSubmission;
use Application\Entity\Homework;
use Application\Entity\Lesson;
use Application\Entity\Session;
use Application\Model\AssignmentSubmissionTable;
use Application\Model\AssignmentTable;
use Application\Model\HomeworkTable;
use Application\Model\SessionTable;
use Application\Model\StudentSessionTable;
use Intermatics\HelperTrait;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


class AssignmentsController extends Controller {

    use HelperTrait;

    protected $uploadDir;


    public function __construct(ContainerInterface $container){

        parent::__construct($container);

        $user= '';
        if(defined('USER_ID')){
            $user = '/'.USER_ID;
        }
        $this->uploadDir = 'usermedia'.$user.'/student_uploads/'.date('Y_m');



    }

    private function makeUploadDir(){
        $path = 'public/'.$this->uploadDir;
        if(!file_exists($path)){
            mkdir($path,0777,true);
        }
    }

    public function assignments(Request $request,Response $response,$args){

        $params= $request->getQueryParams();

        $studentId = $this->getApiStudentId();

        $studentSessionTable = new StudentSessionTable();
        $submissionTable = new AssignmentSubmissionTable();

        $page= !empty($params['page']) ? $params['page']:1;

        $rowsPerPage=30;
        $total = $studentSessionTable->getTotalAssignments($studentId);
        $totalPages = ceil($total/$rowsPerPage);
        $records = [];
        if($page <= ($totalPages)){

        $paginator = $studentSessionTable->getAssignments($studentId);
        $paginator->setCurrentPageNumber((int) $page);
        $paginator->setItemCountPerPage($rowsPerPage);

            foreach($paginator as $row){
                $data= $row;
                $data->has_submission = !empty($submissionTable->hasSubmission($studentId,$row->assignment_id));
                if($data->has_submission){
                    $data->submission = $submissionTable->getAssignment($row->assignment_id,$this->getApiStudentId());
                }
                $records[] =$data;
            }
        }

        return jsonResponse([
            'total_pages'=>$totalPages,
            'current_page'=>$page,
            'total'=> $total,
            'rows_per_page'=>$rowsPerPage,
            'records'=>$records,
        ]);

    }

    public function getAssignment(Request $request,Response $response,$args){
        $id = $args['id'];
        $submissionTable = new AssignmentSubmissionTable();

        $data = Assignment::find($id)->toArray();
        $studentId= $this->getApiStudentId();
        $data['has_submission'] = !empty($submissionTable->hasSubmission($studentId,$data['assignment_id']));
        if($data['has_submission']){
            $data['submission'] = $submissionTable->getAssignment($data['assignment_id'],$this->getApiStudentId());
        }

        return jsonResponse($data);
    }

    public function createSubmission(Request $request,Response $response,$args){
        $data = $request->getParsedBody();



        $id = $data['assignment_id'];
        $assignmentTable = new AssignmentTable();
        $assignmentSubmissionTable = new AssignmentSubmissionTable();
        $assignmentRow = $assignmentTable->getRecord($id);

        $this->validateAssignment($id);
        $output = [];
        $rules =[
            'content'=>'required',
            'assignment_id'=>'required'
        ];
        if($assignmentRow->assignment_type=='f' || $assignmentRow->assignment_type=='b'){
            $file = $_FILES['file_path']['name'];
            if(empty($file)){
                $rules['file']='required';
            }

        }

        $isValid= $this->validate($data,$rules);

        if(!$isValid){
            return jsonResponse(['status'=>false,'msg'=>$this->getValidationErrors()]);
        }

        if($assignmentSubmissionTable->hasSubmission($this->getApiStudentId(),$id)){

            $submissionRow = $assignmentSubmissionTable->getAssignment($id,$this->getApiStudentId());
            if($this->canEdit($submissionRow->assignment_submission_id)){

                return jsonResponse([
                    'status'=>false,
                    'msg'=>'You have already submitted this assignment. You can still edit it',
                    'redirect'=>true,
                    'id'=>$submissionRow->assignment_submission_id
                ]);

            }
            else{
                return jsonResponse([
                    'status'=>false,
                    'msg'=>'You have already submitted this assignment and you can no longer edit it',
                    'redirect'=>false,
                ]);

            }

        }


        //handle file upload
        if($assignmentRow->assignment_type=='f' || $assignmentRow->assignment_type=='b'){
            $file = $_FILES['file_path']['name'];


            if(!isValidUpload($_FILES['file_path']['tmp_name'])){
                return jsonResponse(['status'=>false,'msg'=>'Please upload a pdf, zip file, word document or an image ' ]);

            }

            $newPath = $this->uploadDir.'/'.time().$this->getApiStudentId().'_'.sanitize($this->getApiStudent()->first_name.'_'.$this->getApiStudent()->last_name).'.'.getExtensionForMime($_FILES['file_path']['tmp_name']);
            $this->makeUploadDir();
            rename($_FILES['file_path']['tmp_name'],'public/'.$newPath);

            try{
                chmod('public/'.$newPath,644);
            }
            catch(\Exception $ex){

            }


            $data['file_path'] = $newPath;
        }
        //$uri = $request->getUri();
       // $baseUrl = $uri->getBaseUrl();

       // $content = $this->saveInlineImages($data['content'],$baseUrl);


        $data['content'] = nl2br(strip_tags($data['content']));
        $data['student_id'] = $this->getApiStudentId();
        $data['assignment_id'] = $id;
        $data['created'] = time();
        $data['modified'] = time();
        $data['editable'] = 1;


        $aid = $assignmentSubmissionTable->addRecord($data);
        if($data['submitted']==1 && !empty($aid)){
            $student = $this->getApiStudent();
            $message = $student->first_name.' '.$student->last_name.' just submitted a homework entry for "'.$assignmentRow->title.'"';
            $this->notifyAdmin($assignmentRow->account_id,'New homework submission',$message);
        }

        return jsonResponse([
            'status'=>true,
            'record'=>AssignmentSubmission::find($aid)
        ]);

    }

    public function updateSubmission(Request $request,Response $response,$args){


        $submissionId = $args['id'];
        $data = $request->getParsedBody();


        $assignmentTable = new AssignmentTable();
        $assignmentSubmissionTable = new AssignmentSubmissionTable();
        $row = $assignmentSubmissionTable->getRecord($submissionId);

        $assignmentId = $row->assignment_id;
        $assignmentRow = $assignmentTable->getRecord($assignmentId);


        $output = [];
        $rules =[
            'content'=>'required',
        ];

        if($assignmentRow->assignment_type=='f' || $assignmentRow->assignment_type=='b'){
            $file = $_FILES['file_path']['name'];
            if(empty($file)){
                $rules['file']='required';
            }

        }

        $isValid= $this->validate($data,$rules);

        if(!$isValid){
            return jsonResponse(['status'=>false,'msg'=>$this->getValidationErrors()]);
        }



        if(!$this->canEdit($submissionId)){


            return jsonResponse([
                'status'=>false,
                'msg'=>'Sorry, you can no longer edit this submission',
                'redirect'=>false,
            ]);

        }




        //handle file upload
        if($assignmentRow->assignment_type=='f' || $assignmentRow->assignment_type=='b'){
            @unlink('public/'.$row->file_path);


            $file = $_FILES['file_path']['name'];


            if(!isValidUpload($_FILES['file_path']['tmp_name'])){
                return jsonResponse(['status'=>false,'msg'=>'Please upload a pdf, zip file, word document or an image ' ]);

            }

            $newPath = $this->uploadDir.'/'.time().$this->getApiStudentId().'_'.sanitize($this->getApiStudent()->first_name.'_'.$this->getApiStudent()->last_name).'.'.getExtensionForMime($_FILES['file_path']['tmp_name']);
            $this->makeUploadDir();
            rename($_FILES['file_path']['tmp_name'],'public/'.$newPath);

            try{
                chmod('public/'.$newPath,644);
            }
            catch(\Exception $ex){

            }


            $data['file_path'] = $newPath;


        }
//        $uri = $request->getUri();
//        $baseUrl = $uri->getBaseUrl();
//
//        $content = $this->saveInlineImages($data['content'],$baseUrl);
//
//        $data['content'] = sanitizeHtml($content);

        $data['content'] = nl2br(strip_tags($data['content']));
        $data['modified'] = time();

         $assignmentSubmissionTable->update($data,$submissionId);


        return jsonResponse([
            'status'=>true,
            'record'=>AssignmentSubmission::find($submissionId)
        ]);

    }

    public function deleteSubmission(Request $request,Response $response,$args){
        $id = $args['id'];
        $assignmentSubmissionTable = new AssignmentSubmissionTable();
        $assignmentTable = new AssignmentTable();

        //get assignment
        $row = $assignmentSubmissionTable->getRecord($id);
        $assignmentRow = $assignmentTable->getRecord($row->assignment_id);
        if(empty($row->editable) || ( $assignmentRow->allow_late!=1 && $assignmentRow->due_date < time())){
            return jsonResponse([
                'status'=>false,
                'msg'=> 'Sorry, you can no longer delete this submission'
            ]);

        }

        $this->validateApiOwner($row);

        if(!empty($row->file_path)){
            @unlink($row->file_path);
        }

        $assignmentSubmissionTable->deleteRecord($id);
        return jsonResponse([
            'status'=>true,
            'msg'=> 'Homework deleted',
            'assignment'=>$assignmentRow
        ]);
    }


    private function validateAssignment($id){
        $assignmentTable = new AssignmentTable();
        $studentSessionTable = new StudentSessionTable();
        $assignmentRow = $assignmentTable->getRecord($id);

        if( $assignmentRow->allow_late!=1 && $assignmentRow->due_date < time()){
            return jsonResponse([
                'status'=>false,
                'msg'=>'This assignment is passed its due date!'
            ]);

        }

        if(!$studentSessionTable->enrolled($this->getApiStudentId(),$assignmentRow->session_id)){
            return jsonResponse([
                'status'=>false,
                'msg'=>'You are not enrolled in this session!'
            ]);

        }

    }

    private function canEdit($id){
        $assignmentSubmissionTable = new AssignmentSubmissionTable();
        $assignmentTable = new AssignmentTable();

        //get assignment
        $row = $assignmentSubmissionTable->getRecord($id);
        $assignmentRow = $assignmentTable->getRecord($row->assignment_id);
        if(empty($row->editable) || ( $assignmentRow->allow_late!=1 && $assignmentRow->due_date < time())){
            return false;
        }
        else{
            return true;
        }

    }

    public function revisionNotesSessions(Request $request,Response $response,$args)
    {

        $forumTopicsTable = new HomeworkTable();
        $params = $request->getQueryParams();

        $page = !empty($params['page']) ? $params['page'] : 1;

        $rowsPerPage = 30;

        $studentSessionTable = new StudentSessionTable();
        $studentId = $this->getApiStudentId();

        $total = $studentSessionTable->getTotalStudentForumRecords($studentId);

        $totalPages = ceil($total / $rowsPerPage);
        $records = [];

        if ($page <= $totalPages) {
            $paginator = $studentSessionTable->getStudentForumRecords(true, $studentId);
            $paginator->setCurrentPageNumber((int)$page);
            $paginator->setItemCountPerPage($rowsPerPage);

            foreach ($paginator as $row) {
                $row->total_notes = Session::find($row->session_id)->revisionNotes()->count();
                $records[] = $row;
            }

        }

        return jsonResponse([
            'total_pages' => $totalPages,
            'current_page' => $page,
            'total' => $total,
            'rows_per_page' => $rowsPerPage,
            'records' => $records,
        ]);

    }


    public function revisionNotes(Request $request,Response $response,$args){

        $params = $request->getQueryParams();

        $this->validateParams($params,[
           'course_id'=>'required'
        ]);

        $table = new HomeworkTable($this->getServiceLocator());
        $sessionTable = new SessionTable($this->getServiceLocator());

        $id = $params['course_id'];

        if(!$this->enrolledInSession($id)){
            return jsonResponse(['status'=>false]);
        }

        /*
        $paginator = $table->getPaginatedRecords(true,$id);
        $session = $sessionTable->getRecord($id);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        */

        $rowset = Homework::where('session_id',$id)->paginate(30);

        $data = $rowset->toArray();

        foreach($data['data'] as $key=>$value){
            $data['data'][$key]['lesson_name'] = Lesson::find($value['lesson_id'])->lesson_name;
        }

        return jsonResponse($data);



    }

    public function getRevisionNote(Request $request,Response $response,$args){
        $id = $args['id'];
        $row = Homework::find($id);
        if(!$this->enrolledInSession($row->session_id)){
            return jsonResponse(['status'=>false]);
        }

        return jsonResponse($row);
    }

    private function enrolledInSession($id){
        $studentSessionTable = new StudentSessionTable();
        return $studentSessionTable->enrolled($this->getApiStudentId(),$id);
    }

}