<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:22 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class TestOption extends Model {

    protected $table = 'test_option';
    protected $primaryKey = 'test_option_id';
    public $timestamps = false;

}