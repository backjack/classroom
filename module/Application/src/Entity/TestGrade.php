<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 5/25/2018
 * Time: 4:47 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class TestGrade extends Model {
    protected $table = 'test_grade';
    protected $primaryKey = 'test_grade_id';
    public $timestamps = false;

    protected $fillable = ['grade','min','max'];

}