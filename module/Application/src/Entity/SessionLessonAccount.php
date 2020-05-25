<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:14 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class SessionLessonAccount extends Model{

    protected $table = 'session_lesson_account';
    protected $primaryKey = 'session_lesson_account';
    public $timestamps = false;

}