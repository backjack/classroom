<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:20 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class StudentTest extends Model {

    protected $table = 'student_test';
    protected $primaryKey = 'student_test_id';
    public $timestamps = false;


    public function test(){
        return $this->belongsTo(Test::class,'test_id');
    }

}