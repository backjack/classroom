<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:00 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class DiscussionReply extends Model {

    protected $table = 'discussion_reply';
    protected $primaryKey = 'discussion_reply_id';
    public $timestamps = false;


    public function discussion(){
        return $this->belongsTo(Discussion::class,'discussion_id');
    }

}