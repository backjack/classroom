<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:06 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class LessonGroup extends Model {

    protected $table = 'lesson_group';
    protected $primaryKey = 'lesson_group_id';
    public $timestamps = false;

}