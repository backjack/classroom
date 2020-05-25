<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:08 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model {

    protected $table = 'payment_method';
    protected $primaryKey = 'payment_method_id';
    public $timestamps = false;

    public function paymentMethodCurrencies(){
        return $this->hasMany(PaymentMethodCurrency::class,'payment_method_id');
    }

}