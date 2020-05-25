<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 11/22/2018
 * Time: 11:01 AM
 */

namespace Application\Slim\V1\Controller;

use Application\Entity\Student;
use Application\Model\AttendanceTable;
use Application\Model\CertificateLessonTable;
use Application\Model\CertificateTable;
use Application\Model\CertificateTestTable;
use Application\Model\SessionLessonTable;
use Application\Model\SessionTable;
use Application\Model\StudentCertificateTable;
use Application\Model\StudentSessionTable;
use Application\Model\StudentTestTable;
use Dompdf\Dompdf;
use Intermatics\HelperTrait;
use Intermatics\UtilityFunctions;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class CertificateController extends Controller  {

    use HelperTrait;


    public function certificates(Request $request,Response $response,$args){

        $params= $request->getQueryParams();
        $table = new StudentSessionTable();
        $id= $this->getApiStudentId();
        $rowsPerPage = 30;


        $total = $table->getTotalCertificates($id);
        $totalPages = ceil($total/$rowsPerPage);

        $page= !empty($params['page']) ? $params['page']:1;


        $records = [];

        if($page <= ($totalPages)){

            $paginator = $table->getCertificates(true,$id);
            $paginator->setCurrentPageNumber((int)$page);
            $paginator->setItemCountPerPage($rowsPerPage);

            foreach($paginator as $row){
                $data= $row;
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


    public function getCertificate(Request $request,Response $response,$args){
        $certificateTable = new CertificateTable();
        $id = $args['id'];
        if(!$this->canAccessCertificate($id)){ 
            return jsonResponse([
                'status'=>false,
                'msg'=> 'You have not met the requirements for this certificate!'
            ]); 
        }

        if(!$this->canDownload($id)){
             return jsonResponse([
                'status'=>false,
                'msg'=> 'You have exceeded the maximum number of downloads for this certificate'
            ]);
        }

        $url= $this->getBaseApiUrl($request);


        $html = $this->getCertificateHtml($id);
        $html= str_ireplace($url,'./public',$html);


        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $row= $certificateTable->getRecord($id);
        $orientation = ($row->orientation=='p')?'portrait':'landscape';

        $dompdf->setPaper('A4', $orientation);
        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream(safeUrl($row->certificate_name).'.pdf');

        exit();

    }

    public function getCertificateHtml($id){
        $certificateTable = new CertificateTable();
        $sessionLessonTable = new SessionLessonTable();
        $attendanceTable = new AttendanceTable();
        $studentCertificateTable= new StudentCertificateTable();
        //add student record

        $sessionTable = new SessionTable();
        $row = $certificateTable->getRecord($id);
        $sessionRow = $sessionTable->getRecord($row->session_id);
        $student = $this->getApiStudent();

        $studentCertificate = $studentCertificateTable->addStudentEntry($this->getApiStudentId(),$id);
        $name = $student->first_name.' '.$student->last_name;
        $elements = [
            'student_name'=>$name,
            'session_name'=>$sessionRow->session_name,
            'session_start_date'=>date('d/M/Y',$sessionRow->session_date),
            'session_end_date'=>date('d/M/Y',$sessionRow->session_end_date),
            'date_generated'=>date('d/M/Y'),
            'company_name'=> $this->getSetting('general_site_name'),
            'certificate_number' => $studentCertificate->tracking_number
        ];
        //get lessons for session
        $lessons = $sessionLessonTable->getSessionRecords($row->session_id);

        foreach($lessons as $lesson){

            if(!empty($row->any_session)){
                $date = $attendanceTable->getStudentLessonDate($this->getApiStudentId(),$lesson->lesson_id);
            }
            else{
                $date = $attendanceTable->getStudentLessonDateInSession($this->getApiStudentId(),$lesson->lesson_id,$row->session_id);
            }
            if(empty($date)){
                $date = 'N/A';
            }
            $elements['class_date_'.$lesson->lesson_id.'_'.strtoupper(safeUrl($lesson->lesson_name))]=$date;
        }

        $html = $row->html;



        foreach($elements as $key=>$value){
            $key = strtoupper($key);
            $html = str_replace("[$key]",$value,$html);
        }

        return $html;
    }

    public function canDownload($certificateid){
        $certificateTable = new CertificateTable();


        $certificateRow = $certificateTable->getRecord($certificateid);
        $studentId= $this->getId();
        $student  = Student::find($studentId);

        $totalAllowed = $certificateRow->max_downloads;
        $totalDownloaded = $student->studentCertificateDownloads->count();

        if($totalDownloaded >= $totalAllowed && $totalAllowed > 0){
            return false;
        }
        else{
            return true;
        }


    }

    public function canAccessCertificate($certificateid){
        $certificateTable = new CertificateTable();
        $certificateLessonTable = new CertificateLessonTable();
        $certificateTestTable = new CertificateTestTable();
        $certificateAssignmentTable = new \Application\Model\CertificateAssignmentTable();
        $studentSessionTable = new StudentSessionTable();
        $attendanceTable = new AttendanceTable();
        $studentTestTable = new StudentTestTable();
        $studentAssignmentTable = new \Application\Model\AssignmentSubmissionTable();

        $certificateRow = $certificateTable->getRecord($certificateid);
        $studentId= $this->getApiStudentId();
        //check that certificate is active
        if(empty($certificateRow->status)){
            return false;
        }

        //check that student is enrolled in session
        if(!$studentSessionTable->enrolled($this->getApiStudentId(),$certificateRow->session_id)){

            return jsonResponse([
                'status'=>false,
                'msg'=>'You are not enrolled into the course/session required for this certificate'
            ]);
        }

        //check for attendance
        if($certificateLessonTable->getTotalForCertificate($certificateid)>0){

            $mClasses = [];
            $rowset = $certificateLessonTable->getCertificateLessons($certificateid);
            foreach($rowset as $row){
                $mClasses[] = $row->lesson_id;
            }

            if(empty($certificateRow->any_session)){
                $status = $attendanceTable->hasClassesInSession($studentId,$certificateRow->session_id,$mClasses);
            }
            else{
                $status = $attendanceTable->hasClasses($studentId,$mClasses);
            }

            if(!$status){

                return jsonResponse([
                    'status'=>false,
                    'msg'=>'You have outstanding classes required for this certificate. Please take the course/session again and ensure you complete all classes in it'
                ]);

            }


        }

        if($certificateTestTable->getTotalForCertificate($certificateid)>0){
            $rowset = $certificateTestTable->getCertificateRecords($certificateid);
            foreach($rowset as $row)
            {
                $passedTest = $studentTestTable->passedTest($studentId,$row->test_id);
                if(!$passedTest){
                    $testRecord = Test::find($row->test_id);

                    return jsonResponse([
                        'status'=>false,
                        'msg'=>'You need to take the '.$testRecord->name.' test in order to download this certificate'
                    ]);

                }
            }


        }


        if($certificateAssignmentTable->getTotalForCertificate($certificateid)>0){
            $rowset = $certificateAssignmentTable->getCertificateRecords($certificateid);
            foreach($rowset as $row)
            {
                $passedAssignment = $studentAssignmentTable->passedAssignment($studentId,$row->assignment_id);
                if(!$passedAssignment){
                    $assignmentRecord = Assignment::find($row->assignment_id);
                    $this->flashMessenger()->addMessage('You need to submit the assignment '.$assignmentRecord->title.' assignment in order to download this certificate');

                    return jsonResponse([
                        'status'=>false,
                        'msg'=>'You need to submit the assignment '.$assignmentRecord->title.' assignment in order to download this certificate'
                    ]);

                }
            }


        }


        return true;

    }





}