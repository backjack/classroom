<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/9/2018
 * Time: 2:25 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class CouponCategory extends Model{

    protected $table = 'coupon_category';
    protected $primaryKey = 'coupon_category_id';
    public $timestamps = false;
    protected $fillable = ['coupon_id','session_category_id'];

    public function coupon(){
        return $this->belongsTo(Coupon::class,'coupon_id');
    }

    public function sessionCategory(){
        return $this->belongsTo(SessionCategory::class,'session_category_id');
    }

}