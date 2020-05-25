<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:17 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Student extends Model {

    protected $table = 'student';
    protected $primaryKey = 'student_id';
    public $timestamps = false;

    protected $fillable = ['first_name','last_name','mobile_number','email','password','picture','registration_complete','api_token','last_seen','token_expires'];


    public function studentSessions(){
        return $this->hasMany(StudentSession::class,'student_id');
    }

    public function studentLectures(){
        return $this->hasMany(StudentLecture::class,'student_id');
    }

    public function attendance(){
        return $this->hasMany(Attendance::class,'student_id');
    }

    public function assignmentSubmissions(){
        return $this->hasMany(AssignmentSubmission::class,'student_id');
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class,'student_id');
    }

    public function forumTopics(){
        return $this->hasMany(ForumTopic::class,'topic_owner');
    }

    public function forumPost(){
        return $this->hasMany(ForumPost::class,'post_owner');
    }

    public function studentTests(){
        return $this->hasMany(StudentTest::class,'student_id');
    }

    public function studentCertificates(){
        return $this->hasMany(StudentCertificate::class,'student_id');
    }

    public function studentCertificateDownloads(){
        return $this->hasMany(StudentCertificateDownload::class,'student_id');
    }




}