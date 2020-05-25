<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 5/5/2018
 * Time: 2:36 PM
 */

namespace Application\Model;


use Application\Entity\ForumParticipant;
use Intermatics\BaseTable;

class ForumParticipantTable extends BaseTable {

    protected $tableName = 'forum_participant';
    protected $primary = 'forum_participant_id';

    public function updateParticipant($topic,$user,$user_type,$notify=1){

        $participant = ForumParticipant::where('forum_topic_id',$topic)->where('user_id',$user)->where('user_type',$user_type)->first();

        if(!$participant){
            ForumParticipant::create([
                'forum_topic_id'=>$topic,
                'user_id'=>$user,
                'user_type'=>$user_type,
                'notify'=>$notify
            ]);
        }else{
            $participant->notify = $notify;
            $participant->save();
        }
    }

}