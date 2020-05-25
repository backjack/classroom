<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:00 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class DiscussionAccount extends Model {

    protected $table = 'discussion_account';
    protected $primaryKey = 'discussion_account_id';
    public $timestamps = false;

}