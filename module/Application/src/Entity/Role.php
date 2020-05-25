<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:11 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Role extends Model {

    protected $table = 'role';
    protected $primaryKey = 'role_id';
    public $timestamps = false;

}