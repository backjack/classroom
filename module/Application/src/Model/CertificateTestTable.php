<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 6/13/2017
 * Time: 1:55 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class CertificateTestTable extends BaseTable {

    protected $tableName = 'certificate_test';
    protected $primary = 'certificate_id';

    public function clearCertificateRecords($id){
        return $this->tableGateway->delete(['certificate_id'=>$id]);
    }

    public function getCertificateRecords($id){

        $select = new Select($this->tableName);
        $select->where(['certificate_id'=>$id])
            ->join('test',$this->tableName.'.test_id=test.test_id',['name','passmark']);
        return $this->tableGateway->selectWith($select);
    }

    public function getCertificateTests($id){
        return $this->tableGateway->select(['certificate_id'=>$id]);
    }

    public function getTotalForCertificate($certificateId){
        $total = $this->tableGateway->select(['certificate_id'=>$certificateId])->count();
        return $total;

    }


}