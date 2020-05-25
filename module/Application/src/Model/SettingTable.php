<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 1/6/2017
 * Time: 9:09 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Intermatics\UtilityFunctions;
use Zend\Db\Sql\Select;

class SettingTable extends BaseTable {

    protected $tableName = 'setting';
    protected $primary = 'setting_id';

    public function getSettings(){
        $row = $this->tableGateway->select()->current();
        $rowset = $this->getRecords();
        $settings = [];
        foreach($rowset as $row){
            if(empty($row->serialized)){
                $settings[$row->key] = $row->value;
            }
            else{
                $settings[$row->key] = unserialize($row->value);
            }

        }

        return $settings;
    }

    public function getSetting($setting){

        $row = $this->tableGateway->select(['key'=>$setting])->current();
        if(empty($row->serialized)){
            $val = $row->value;
        }
        else{
            $val = unserialize($row->value);
        }


        return $val;
    }

    public function saveSetting($key,$value){
       $total= $this->tableGateway->update(['value'=>$value],['key'=>$key]);
        return $total;
    }

    public function getSettingGroup($group){

        $select = new Select($this->tableName);
        $select->where('setting.key LIKE \''.$group.'_%\'');
        $rowset = $this->tableGateway->selectWith($select);
        return $rowset;

    }

}