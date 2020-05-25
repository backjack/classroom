<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:12 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class SessionCategory extends Model {

    protected $table = 'session_category';
    protected $primaryKey = 'session_category_id';
    public $timestamps = false;

    public function sessionToSessionCategory(){
        return $this->hasMany(SessionToSessionCategory::class,'session_category_id');
    }

}