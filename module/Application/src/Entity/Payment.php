<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:07 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Payment extends Model {

    protected $table = 'payment';
    protected $primaryKey = 'payment_id';
    public $timestamps = false;

}