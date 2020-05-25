<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:22 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Test extends Model {

    protected $table = 'test';
    protected $primaryKey = 'test_id';
    public $timestamps = false;

    public function testQuestions(){
        return $this->hasMany(TestQuestion::class,'test_id');
    }

    public function studentTests(){
        return $this->hasMany(StudentTest::class,'test_id');
    }

    public function account(){
        return $this->belongsTo(Account::class,'account_id');
    }

}