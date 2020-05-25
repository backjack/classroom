<?php
namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class SessionSurvey  extends Model {

    protected $table = 'session_survey';
    protected $primaryKey = 'session_survey_id';
    public $timestamps = false;

    public function session(){
        return $this->belongsTo(Session::class,'session_id');
    }

}