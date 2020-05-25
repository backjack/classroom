<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:03 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Homework extends Model {
    protected $table = 'homework';
    protected $primaryKey = 'homework_id';
    public $timestamps = false;

}