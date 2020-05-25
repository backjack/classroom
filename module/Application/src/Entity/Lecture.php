<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:03 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Lecture extends Model {

    protected $table = 'lecture';
    protected $primaryKey = 'lecture_id';
    public $timestamps = false;
    protected $fillable = ['lesson_id','lecture_title','sort_order'];

    public function lesson(){
        return $this->belongsTo(Lesson::class,'lesson_id');
    }

    public function lectureFiles(){
        return $this->hasMany(LectureFile::class,$this->primaryKey);
    }

    public function lectureNotes(){
        return $this->hasMany(LectureNote::class,$this->primaryKey);
    }

    public function lecturePages(){
        return $this->hasMany(LecturePage::class,$this->primaryKey);
    }


}