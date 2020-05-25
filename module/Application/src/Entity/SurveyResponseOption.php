<?php
namespace Application\Entity;

use Illuminate\Database\Eloquent\Model;

class SurveyResponseOption extends Model
{
    protected $table = 'survey_response_option';
    protected $primaryKey = 'survey_response_option_id';
    public $timestamps = false;
    protected $fillable = ['survey_response_id','survey_option_id'];

    public function surveyOption(){
        return $this->belongsTo(SurveyOption::class,'survey_option_id');
    }

    public function surveyResponse(){
        return $this->belongsTo(SurveyResponse::class,'survey_response_id');
    }

}