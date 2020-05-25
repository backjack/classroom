<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:19 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class StudentRegistration extends Model {
    protected $table = 'student_registration';
    protected $primaryKey = 'student_registration_id';
    public $timestamps = false;

}