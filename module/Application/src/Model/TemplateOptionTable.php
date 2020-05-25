<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 2/22/2018
 * Time: 2:15 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class TemplateOptionTable extends BaseTable {

    protected $tableName = 'template_option';
    protected $primary = 'template_option_id';


    public function getGroups($templateId){
        $select = new Select($this->tableName);
        $select->where(['template_id'=>$templateId])
            ->group('group');
            $rowset = $this->tableGateway->selectWith($select);
        return $rowset;
    }

    public function saveOption($tid,$key,$value){
        $total= $this->tableGateway->update(['value'=>$value],['key'=>$key,'template_id'=>$tid,]);
        return $total;
    }

    public function getTemplateRecords($templateId){
        $select = new Select($this->tableName);
        $select->where(['template_id'=>$templateId]) ;
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset;
    }

    public function getRecordsForGroup($tid,$group){
        $select = new Select($this->tableName);
        $select->where(['template_id'=>$tid,'group'=>$group])
                ->order('sort_order');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset;
    }

    public function getOption($tid,$setting){

        $row = $this->tableGateway->select(['key'=>$setting,'template_id'=>$tid])->current();

        $val = $row->value;


        return $val;
    }

}