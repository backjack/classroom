<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:02 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model {

    protected $table = 'forum_post';
    protected $primaryKey = 'forum_post_id';
    public $timestamps = false;
    protected $fillable = ['forum_topic_id','message','post_created_on','post_owner','post_owner_type','post_reply_id'];

    public function forumTopic(){
        return $this->belongsTo(ForumTopic::class,'forum_topic_id');
    }



}