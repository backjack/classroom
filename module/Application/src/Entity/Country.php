<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 12:59 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Country extends Model {

    protected $table = 'country';
    protected $primaryKey = 'country_id';
    public $timestamps = false;

}