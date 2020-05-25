<?php
namespace Application\Entity;

use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    protected $table = 'survey_response';
    protected $primaryKey = 'survey_response_id';
    public $timestamps = false;
    protected $fillable = ['created_on','student_id','survey_id'];

    public function survey(){
        return $this->belongsTo(Survey::class,'survey_id');
    }

    public function surveyResponseOptions(){
        return $this->hasMany(SurveyResponseOption::class,'survey_response_id');
    }

}