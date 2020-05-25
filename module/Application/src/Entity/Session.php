<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:12 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Session extends Model {

    protected $table = 'session';
    protected $primaryKey = 'session_id';
    public $timestamps = false;
    protected $guarded= ['session_id'];

    public function forumTopics(){
        return $this->hasMany(ForumTopic::class,'session_id');
    }

    public function revisionNotes(){
        return $this->hasMany(Homework::class,'session_id');
    }

    public function account(){
        return $this->belongsTo(Account::class,'account_id');
    }

    public function sessionInstructors(){
        return $this->hasMany(SessionInstructor::class,'session_id');
    }

    public function sessionLessons(){
        return $this->hasMany(SessionLesson::class,'session_id');
    }

    public function studentSessions(){
        return $this->hasMany(StudentSession::class,'session_id');
    }

    public function sessionTests(){
        return $this->hasMany(SessionTest::class,'session_id');
    }

    public function assignments(){
        return $this->hasMany(Assignment::class,'session_id');
    }


}