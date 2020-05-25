<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Model;

use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

/**
 * Description of CertificateAssignmentTable
 *
 * @author ayokunle
 */
class CertificateAssignmentTable extends BaseTable {
    protected $tableName = 'certificate_assignment';
    protected $primary = 'certificate_id';

    public function clearCertificateRecords($id){
        return $this->tableGateway->delete(['certificate_id'=>$id]);
    }

    public function getCertificateRecords($id){

        $select = new Select($this->tableName);
        $select->where(['certificate_id'=>$id])
            ->join('assignment',$this->tableName.'.assignment_id=assignment.assignment_id',['title','passmark']);
        return $this->tableGateway->selectWith($select);
    }

    public function getCertificateAssignments($id){
        return $this->tableGateway->select(['certificate_id'=>$id]);
    }

    public function getTotalForCertificate($certificateId){
        $total = $this->tableGateway->select(['certificate_id'=>$certificateId])->count();
        return $total;

    }

}
