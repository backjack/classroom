<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:04 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class LectureFile extends Model {
    protected $table = 'lecture_file';
    protected $primaryKey = 'lecture_file_id';
    public $timestamps = false;
    protected $fillable = ['lecture_id','path','created_on','status'];

}