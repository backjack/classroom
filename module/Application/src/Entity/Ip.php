<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/9/2018
 * Time: 3:05 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Ip extends Model {

    protected $table = 'ip';
    protected $primaryKey = 'ip_id';
    public $timestamps = false;

    protected $fillable = ['ip','country'];
}