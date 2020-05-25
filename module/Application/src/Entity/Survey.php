<?php
namespace Application\Entity;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $primaryKey = 'survey_id';
    public $timestamps = false;
    protected $table = 'survey';


    public function account(){
        return $this->belongsTo(Account::class,'account_id');
    }


    public function surveyQuestions(){
        return $this->hasMany(SurveyQuestion::class,'survey_id');
    }

    public function surveyResponses(){
        return $this->hasMany(SurveyResponse::class,'survey_id');
    }

}