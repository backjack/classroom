<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 12:54 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model {
    protected $table = 'bookmark';
    protected $primaryKey = 'bookmark_id';
    public $timestamps = false;
    protected $guarded = ['bookmark_id'];
}