<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:10 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class RegistrationField extends Model {

    protected $table = 'registration_field';
    protected $primaryKey = 'registration_field_id';
    public $timestamps = false;

}