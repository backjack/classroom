<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:13 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class SessionLesson extends Model {

    protected $table = 'session_lesson';
    protected $primaryKey = 'session_lesson_id';
    public $timestamps = false;

    public function session(){
        return $this->belongsTo(Session::class,'session_id');
    }

    public function lesson(){
        return $this->belongsTo(Lesson::class,'lesson_id');
    }

}