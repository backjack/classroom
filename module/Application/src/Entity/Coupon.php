<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/9/2018
 * Time: 2:25 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Coupon extends Model{

    protected $table = 'coupon';
    protected $primaryKey = 'coupon_id';
    public $timestamps = false;
    protected $fillable = ['code','discount','expires','enabled','name','type','total','date_start','uses_total','uses_customer'];

    public function couponSessions(){
        return $this->hasMany(CouponSession::class,'coupon_id');
    }

    public function couponCategories(){
        return $this->hasMany(CouponCategory::class,'coupon_id');
    }

    public function couponInvoices(){
        return $this->hasMany(CouponInvoice::class,'coupon_id');
    }

}