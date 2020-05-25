<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 12:52 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Assignment extends Model {

    protected $table = 'assignment';
    protected $primaryKey = 'assignment_id';
    public $timestamps = false;

    public function assignmentSubmissions(){
        return $this->hasMany(AssignmentSubmission::class,'assignment_id');
    }

    public function account(){
        return $this->belongsTo(Account::class,'account_id');
    }

}