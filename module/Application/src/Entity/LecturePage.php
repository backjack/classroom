<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:04 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class LecturePage extends Model{

    protected $table = 'lecture_page';
    protected $primaryKey = 'lecture_page_id';
    public $timestamps = false;
    protected $fillable = ['lecture_id','title','content','type','sort_order','audio_code'];

    public function lecture(){
        return $this->belongsTo(Lecture::class,'lecture_id');
    }

}