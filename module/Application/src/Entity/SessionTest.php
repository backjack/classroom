<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:14 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class SessionTest extends Model {

    protected $table = 'session_test';
    protected $primaryKey = 'session_test_id';
    public $timestamps = false;

}