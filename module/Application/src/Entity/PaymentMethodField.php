<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:08 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class PaymentMethodField extends Model {

    protected $table = 'payment_method_field';
    protected $primaryKey = 'payment_method_field_id';
    public $timestamps = false;

}