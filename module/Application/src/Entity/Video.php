<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 9/15/2018
 * Time: 10:53 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $table = 'video';
    protected $primaryKey = 'video_id';
    public $timestamps = false;


    public function account(){
        return $this->belongsTo(Account::class,'account_id');
    }
}