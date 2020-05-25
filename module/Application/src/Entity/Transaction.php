<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:24 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {

    protected $table = 'transaction';
    protected $primaryKey = 'transaction_id';
    public $timestamps = false;


    public function student(){
        return $this->belongsTo(Student::class,'student_id');
    }

    public function session(){
        return $this->belongsTo(Session::class,'session_id');
    }

    public function paymentMethod(){
        return $this->belongsTo(PaymentMethod::class,'payment_method_id');
    }
}