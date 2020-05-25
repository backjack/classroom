<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:40 PM
 */

namespace Application\Entity;



use Illuminate\Database\Eloquent\Model;

class LectureNote extends Model{
    protected $table = 'lecture_note';
    protected $primaryKey = 'lecture_note_id';
    public $timestamps = false;
}