<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:02 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class ForumTopic extends Model {

    protected $table = 'forum_topic';
    protected $primaryKey = 'forum_topic_id';
    public $timestamps = false;
    protected $fillable = ['created_on','topic_owner','topic_owner_type','session_id','lecture_id','topic_title'];

    public function forumPosts(){
        return $this->hasMany(ForumPost::class,$this->primaryKey);
    }

    public function session(){
        return $this->belongsTo(Session::class,'session_id');
    }

    public function lecture(){
        return $this->belongsTo(Lecture::class,'lecture_id');
    }

    public function forumParticipants(){
        return $this->hasMany(ForumParticipant::class,'forum_topic_id');
    }
}