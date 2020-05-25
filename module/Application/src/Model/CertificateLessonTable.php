<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2017
 * Time: 3:46 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;

class CertificateLessonTable extends BaseTable {

    protected $tableName = 'certificate_lesson';
    protected $primary = 'certificate_id';

    public function hasLesson($certificateId,$lessonId){

        $total = $this->tableGateway->select(['certificate_id'=>$certificateId,'lesson_id'=>$lessonId])->count();

        if(empty($total))
        {
            return false;
        }
        else{
            return true;
        }

    }

    public function getTotalForCertificate($certificateId){
        $total = $this->tableGateway->select(['certificate_id'=>$certificateId])->count();
        return $total;

    }

    public function clearCertificateRecords($id){
        return $this->tableGateway->delete(['certificate_id'=>$id]);
    }

    public function getCertificateLessons($id){
        return $this->tableGateway->select(['certificate_id'=>$id]);
    }
}