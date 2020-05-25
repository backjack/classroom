<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:09 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Permission extends Model {
    protected $table = 'permission';
    protected $primaryKey = 'persmision_id';
    public $timestamps = false;

}