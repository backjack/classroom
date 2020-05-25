<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:09 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class PermissionGroup extends Model {

    protected $table = 'permission_group';
    protected $primaryKey = 'permission_group_id';
    public $timestamps = false;

}