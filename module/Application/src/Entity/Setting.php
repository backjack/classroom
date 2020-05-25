<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:15 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Setting extends Model {
    protected $table = 'setting';
    protected $primaryKey = 'setting_id';
    public $timestamps = false;

}