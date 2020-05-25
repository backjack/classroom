<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:16 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class SmsGateway extends Model {

    protected $table = 'sms_gateway';
    protected $primaryKey = 'sms_gateway_id';
    public $timestamps = false;

}