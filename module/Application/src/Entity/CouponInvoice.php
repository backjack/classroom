<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/9/2018
 * Time: 2:25 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class CouponInvoice extends Model{

    protected $table = 'coupon_invoice';
    protected $primaryKey = 'coupon_invoice_id';
    public $timestamps = false;
    protected $fillable = ['coupon_id','invoice_id'];


    public function coupon(){
        return $this->belongsTo(Coupon::class,'coupon_id');
    }

    public function invoice(){
        return $this->belongsTo(Invoice::class,'invoice_id');
    }

}