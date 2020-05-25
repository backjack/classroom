<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 1/28/2017
 * Time: 2:07 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;

class PermissionGroupTable extends BaseTable {

    protected $tableName = 'permission_group';
    protected $primary = 'permission_group_id';

}