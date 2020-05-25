<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:21 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Template extends Model {

    protected $table = 'template';
    protected $primaryKey = 'template_id';
    public $timestamps = false;

}