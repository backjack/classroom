<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:21 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class StudentTestOption extends Model {

    protected $table = 'student_test_option';
    protected $primaryKey = 'student_test_option_id';
    public $timestamps = false;

}