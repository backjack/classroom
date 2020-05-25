<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 12:53 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model {

    protected $table = 'assignment_submission';
    protected $primaryKey = 'assignment_submission_id';
    public $timestamps = false;

}