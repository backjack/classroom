<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:23 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class TestQuestion extends Model {

    protected $table = 'test_question';
    protected $primaryKey = 'test_question_id';
    public $timestamps = false;

    public function testOptions(){
        return $this->hasMany(TestOption::class,'test_question_id');
    }

}