<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 8/3/2018
 * Time: 5:31 PM
 */

namespace Application\Model;


use Application\Entity\Certificate;
use Application\Entity\StudentCertificate;
use Application\Entity\StudentCertificateDownload;
use Intermatics\BaseTable;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class StudentCertificateTable extends BaseTable
{
    protected $tableName = 'student_certificate';
    protected $primary = 'student_certificate_id';

    public function addStudentEntry($studentId,$certificateId){
        StudentCertificateDownload::create([
           'student_id'=>$studentId,
            'certificate_id'=>$certificateId,
            'created_on'=>time()
        ]);


        $certificate = Certificate::find($certificateId);
        $student = \Application\Entity\Student::find($studentId);

        //check if student has record
        $row = StudentCertificate::where('student_id',$studentId)->where('certificate_id',$certificateId)->first();
        if($row){
            return $row;
        }

        $trackingNo = date('y',$certificate->created_on).'-'.$student->student_id.'-'.$certificate->certificate_id;

        $row  = StudentCertificate::where('tracking_number',$trackingNo)->first();
        if($row){
            return $row;
        }



        $studentCertificate  = StudentCertificate::create([
            'student_id' => $studentId,
            'certificate_id' => $certificateId,
            'created_on' => time(),
            'tracking_number' => $trackingNo
        ]);

        return $studentCertificate;
    }


    public function searchStudents($filter)
    {
        $select = new Select($this->tableName);
        $select->join('student',$this->tableName.'.student_id=student.student_id',['first_name','last_name','email']);


            $filter = $this->db->escape(trim($filter));
            //$select->where('(student.first_name LIKE \'%'.$filter.'%\' OR student.last_name LIKE \'%'.$filter.'%\' OR student.email LIKE \'%'.$filter.'%\')');
            $select->where("MATCH (student.first_name,student.last_name,student.email) AGAINST ('$filter' IN NATURAL LANGUAGE MODE) OR tracking_number='$filter'");


            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;

    }

}