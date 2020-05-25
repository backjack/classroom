<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:20 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class StudentVideo extends Model {

    protected $table = 'student_video';
    protected $primaryKey = 'student_video_id';
    public $timestamps = false;
    protected $fillable = ['student_id','video_id'];


    public function video(){
        return $this->belongsTo(Video::class,'video_id');
    }

    public function student(){
        return $this->belongsTo(Student::class,'student_id');
    }

}