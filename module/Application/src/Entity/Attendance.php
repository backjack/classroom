<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 12:53 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Attendance extends Model {

    protected $table = 'attendance';
    protected $primaryKey = 'attendance_id';
    public $timestamps = false;

}