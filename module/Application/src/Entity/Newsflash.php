<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:06 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Newsflash extends Model {

    protected $table = 'newsflash';
    protected $primaryKey = 'newsflash_id';
    public $timestamps = false;
    protected $guarded = ['newsflash_id'];

}