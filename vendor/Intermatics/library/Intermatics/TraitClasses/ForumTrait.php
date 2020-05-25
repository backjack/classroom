<?php
namespace Intermatics\TraitClasses;

use Application\Entity\ForumPost;
use Application\Entity\ForumTopic;
use Intermatics\BaseForm;
use Intermatics\HelperTrait;
use Zend\InputFilter\InputFilter;

trait ForumTrait {


    private function forumTopicForm(){
        $form = new BaseForm();
        $form->createText('topic_title','Topic',true,null,null,'Enter the topic for this thread');
        $form->createTextArea('message','Post',true,null,'Enter the first post');
        $form->get('message')->setAttribute('class','form-control summernote');
        $form->setInputFilter($this->forumTopicFilter());
        return $form;
    }

    private function forumTopicFilter(){
        $filter = new InputFilter();
        $filter->add([
            'name'=>'topic_title',
            'required'=>true,
            'validators'=>[
                [
                    'name'=>'NotEmpty'
                ]
            ]
        ]);

        $filter->add([
            'name'=>'message',
            'required'=>true,
            'validators'=>[
                [
                    'name'=>'NotEmpty'
                ]
            ]
        ]);

        return $filter;
    }

    public function notifyParticipants($forumPostId,$api=false,$basepath=false){
        $forumPost = ForumPost::find($forumPostId);
        $forumTopic = $forumPost->forumTopic;

        foreach($forumTopic->forumParticipants as $participant){
            if(empty($participant->notify)){
                continue;
            }
            $user = forumUser($participant->user_id,$participant->user_type);
            $data=[
                'name'=>$user['name'],
                'post'=>$forumPost,
               ];

            if($participant->user_type=='s'){
                if($api){
                    $data['url'] = $basepath.'/student/forum/topic/'.$forumPost->forum_topic_id;
                }
                else{
                    $data['url']= $this->url()->fromRoute('application/default',['controller'=>'forum','action'=>'topic','id'=>$forumPost->forum_topic_id],['force_canonical'=>true]);

                }

            }
            else{
                if($api){
                    $data['url'] = $basepath.'/admin/forum/topic/'.$forumPost->forum_topic_id;
                }
                else{
                    $data['url']= $this->url()->fromRoute('admin/default',['controller'=>'forum','action'=>'topic','id'=>$forumPost->forum_topic_id],['force_canonical'=>true]);
                }
            }

            //get blade
            if(!$api){
                $message = $this->bladeHtml('mails.forum_reply',$data);
            }
            else{
                extract($data);
                $name = $data['name'];
                $replyier = forumUser($forumPost->post_owner,$forumPost->post_owner_type)['name'];
                $title = $forumPost->forumTopic->topic_title;
                $fmessage = $forumPost->message;
                $url = $data['url'];
                $message =<<<EOD
Dear  $name , <br/>
$replyier just replied to the topic '$title'. $replyier said:
<br/><br/>
<div style="border: solid 1px #cccccc; padding: 10px">
    $fmessage
</div>

<br/>
Click here to reply now: <a href="{$url}">{$url}</a>

EOD;

            }


            try{
                $this->sendEmail($user['email'],'New Forum Reply: '.$forumTopic->topic_title,$message);
            }
            catch(\Exception $ex){

            }

        }


    }

}