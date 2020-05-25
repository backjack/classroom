<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 5/5/2018
 * Time: 2:33 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class ForumParticipant extends Model {

    protected $table = 'forum_participant';
    protected $primaryKey = 'forum_participant_id';
    public $timestamps = false;
    protected $fillable = ['user_id','user_type','notify','forum_topic_id'];

    public function forumTopic(){
        return $this->belongsTo(ForumTopic::class,'forum_topic_id');
    }

}