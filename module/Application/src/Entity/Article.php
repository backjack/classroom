<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 12:50 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Article extends Model {

    protected $table = 'articles';
    protected $primaryKey = 'article_id';
    public $timestamps = false;


}