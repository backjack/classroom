<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:13 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class SessionInstructor extends Model {

    protected $table = 'session_instructor';
    protected $primaryKey = 'session_instructor_id';
    public $timestamps = false;

    public function account(){
        return $this->belongsTo(Account::class,'account_id');
    }

    public function session(){
        return $this->belongsTo(Session::class,'session_id');
    }
}