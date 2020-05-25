<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:19 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class StudentSession extends Model {

    protected $table = 'student_session';
    protected $primaryKey = 'student_session_id';
    public $timestamps = false;

    public function session(){
        return $this->belongsTo(Session::class,'session_id');
    }

}