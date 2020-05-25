<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 1/21/2017
 * Time: 1:17 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class WidgetValueTable extends BaseTable {

    protected $tableName = 'widget_value';
    protected $primary = 'widget_value_id';

    public function getWidgets($enabled=null,$visibility=null){
        $select = new Select($this->tableName);
        $select->order('sort_order asc');
        $select->join('widget', $this->tableName.'.widget_id=widget.widget_id');
        if($enabled){
            $select->where(['enabled'=>$enabled]);
        }

        if($visibility){
            $select->where("visibility='{$visibility}' OR visibility='b'");
        }

        $rowset = $this->tableGateway->selectWith($select);
        return $rowset;
    }

    public function saveOptions($merchantWidgetId,$data)
    {
        $dbData = array(
            'enabled'=>$data['enabled'],
            'sort_order'=>$data['sort_order'],
            'value'=>serialize($data),
            'visibility'=>$data['visibility']
        );


        $this->tableGateway->update($dbData,array('widget_value_id'=>$merchantWidgetId));

        return $merchantWidgetId;



    }

}