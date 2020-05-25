<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:18 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class StudentField extends Model {

    protected $table = 'student_field';
    protected $primaryKey = 'student_field_id';
    public $timestamps = false;

}