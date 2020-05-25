<?php
namespace Application\Entity;

use Illuminate\Database\Eloquent\Model;

class SurveyOption extends Model
{
    protected $table = 'survey_option';
    protected $primaryKey = 'survey_option_id';
    public $timestamps = false;

    public function surveyQuestion(){
        return $this->belongsTo(SurveyQuestion::class,'survey_question_id');
    }

}