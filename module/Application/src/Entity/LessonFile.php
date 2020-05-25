<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:05 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class LessonFile extends Model {

    protected $table = 'lesson_file';
    protected $primaryKey = 'lesson_file_id';
    public $timestamps = false;

}