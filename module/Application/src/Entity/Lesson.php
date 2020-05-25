<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:05 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Lesson extends Model {

    protected $table = 'lesson';
    protected $primaryKey = 'lesson_id';
    public $timestamps = false;



    public function lectures(){
        return $this->hasMany(Lecture::class,'lesson_id');
    }

    public function lessonFiles(){
        return $this->hasMany(LessonFile::class,'lesson_id');
    }


}