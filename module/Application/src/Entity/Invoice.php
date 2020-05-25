<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/6/2018
 * Time: 4:53 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Invoice extends Model {

    protected $table = 'invoice';
    protected $primaryKey = 'invoice_id';
    public $timestamps = false;
    protected $fillable = ['student_id','currency_id','created_on','amount','cart','paid','payment_method_id'];

    public function currency(){
        return $this->belongsTo(Currency::class,'currency_id');
    }

    public function student(){
        return $this->belongsTo(Student::class,'student_id');
    }

    public function paymentMethod(){
        return $this->belongsTo(PaymentMethod::class,'payment_method_id');
    }

}