<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/9/2018
 * Time: 2:25 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class CouponSession extends Model{

    protected $table = 'coupon_session';
    protected $primaryKey = 'coupon_session_id';
    public $timestamps = false;
    protected $fillable = ['coupon_id','session_id'];

    public function coupon(){
        return $this->belongsTo(Coupon::class,'coupon_id');
    }

}