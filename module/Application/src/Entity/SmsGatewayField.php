<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:17 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class SmsGatewayField extends Model {

    protected $table = 'sms_gateway_field';
    protected $primaryKey = 'sms_gateway_field';
    public $timestamps = false;

}