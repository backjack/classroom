<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:24 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Widget extends Model {
    protected $table = 'widget';
    protected $primaryKey = 'widget_id';
    public $timestamps = false;

}