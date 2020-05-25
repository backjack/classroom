<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 12:45 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Account extends Model {

    protected $primaryKey = 'account_id';
    public $timestamps = false;

    public function role(){
        return $this->belongsTo(Role::class,'role_id');
    }

    public function sessions(){
        return $this->hasMany(Session::class,'account_id');
    }


}