<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:07 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model {

    protected $table = 'password_reset';
    protected $primaryKey = 'password_reset_id';
    public $timestamps = false;


}