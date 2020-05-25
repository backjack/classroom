<?php
namespace Application\Entity;

use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    protected $table = 'survey_question';
    protected $primaryKey = 'survey_question_id';
    public $timestamps = false;

    public function survey(){
        return $this->belongsTo(Survey::class,'survey_id');
    }

    public function surveyOptions(){
        return $this->hasMany(SurveyOption::class,'survey_question_id');
    }

}