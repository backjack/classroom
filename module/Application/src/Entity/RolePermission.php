<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:11 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model{

    protected $table = 'role_permission';
    protected $primaryKey = 'role_permission_id';
    public $timestamps = false;

}