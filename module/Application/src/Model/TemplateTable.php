<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 2/22/2018
 * Time: 2:14 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;

class TemplateTable extends BaseTable {

    protected $tableName = 'template';
    protected $primary = 'template_id';

    public function getActiveTemplate(){
        $row = $this->tableGateway->select(['active'=>1])->current();
        return $row;
    }

}