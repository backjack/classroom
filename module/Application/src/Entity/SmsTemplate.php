<?php
/**
 * Created by PhpStorm.
 * User: ayokunle
 * Date: 6/22/18
 * Time: 11:09 AM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    protected $table = 'sms_template';
    protected $primaryKey = 'sms_template_id';
    public $timestamps = false;

}