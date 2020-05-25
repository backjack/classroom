<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:10 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class RelatedCourse extends Model {

    protected $table = 'related_course';
    protected $primaryKey = 'related_course_id';
    public $timestamps = false;

}