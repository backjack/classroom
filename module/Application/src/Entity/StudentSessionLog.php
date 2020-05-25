<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:19 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class StudentSessionLog extends Model {
    protected $table = 'student_session_log';
    protected $primaryKey = 'student_session_log_id';
    public $timestamps = false;
}