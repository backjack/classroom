<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/6/2018
 * Time: 4:52 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Currency extends Model {

    protected $table = 'currency';
    protected $primaryKey = 'currency_id';
    public $timestamps = false;
    protected $fillable = ['country_id','exchange_rate'];

    public function country(){
        return $this->belongsTo(Country::class,'country_id');
    }

}