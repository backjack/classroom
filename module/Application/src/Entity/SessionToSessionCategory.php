<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:15 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class SessionToSessionCategory  extends Model{

    protected $table = 'session_to_session_category';
    protected $primaryKey = 'session_to_session_category_id';
    public $timestamps = false;

}