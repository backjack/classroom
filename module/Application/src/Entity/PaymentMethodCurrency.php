<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/8/2018
 * Time: 1:29 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class PaymentMethodCurrency extends Model {

    protected $table = 'payment_method_currency';
    protected $primaryKey = 'payment_method_currency_id';
    public $timestamps = false;

    public function currency(){
        return $this->belongsTo(Currency::class,'currency_id');
    }

    public function paymentMethod(){
        return $this->belongsTo(PaymentMethod::class,'payment_method_id');
    }

}