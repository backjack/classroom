<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 12:59 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Discussion extends Model {
    protected $table = 'discussion';
    protected $primaryKey = 'discussion_id';
    public $timestamps = false;

    public function discussionReplies(){
        return $this->hasMany(DiscussionReply::class,'discussion_id');
    }

    public function discussionAccounts(){
        return $this->hasMany(DiscussionAccount::class,'discussion_id');
    }

}