<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 8/22/2017
 * Time: 11:11 AM
 */

namespace Application\Controller;

use Application\Entity\ForumParticipant;
use Application\Entity\ForumPost;
use Application\Entity\ForumTopic;
use Application\Entity\Lecture;
use Application\Entity\Session;
use Application\Model\ForumParticipantTable;
use Application\Model\SessionTable;
use Application\Model\StudentSessionTable;
use Intermatics\BaseForm;
use Intermatics\TraitClasses\ForumTrait;
use Intermatics\HelperTrait;
use Zend\EventManager\EventManagerInterface;
use Zend\Form\Element\Checkbox;
use Zend\InputFilter\InputFilter;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Illuminate\Database\Capsule\Manager as DB;


class ForumController extends AbstractController {

    use HelperTrait;
    use ForumTrait;

    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);
        $controller = $this;
        $events->attach('dispatch', function ($e) use ($controller) {
            $controller->layout('layout/student');
        }, 100);
    }



    /**
     * Get list of all sessions student is enrolled in and for which forum is enabled
     */
    public function indexAction(){

        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $studentId = $this->getId();
        $paginator = $studentSessionTable->getStudentForumRecords(true,$studentId);
        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);


        return [
            'paginator'=>$paginator,
            'pageTitle'=>__('Student Forum')
        ];

    }


    public function topicsAction(){
        $sessionId = $this->params('id');
        $this->validateAccess($sessionId);
        $lectureId = $this->request->getQuery('lecture_id');
        $session = Session::find($sessionId);

        if(!empty($lectureId)){
            $topics = $session->forumTopics()->where('lecture_id',$lectureId)->orderBy('forum_topic_id','desc')->paginate(20);

        }
        else{
            $topics = $session->forumTopics()->orderBy('forum_topic_id','desc')->paginate(20);
        }

        $this->data['lecture'] = Lecture::find($lectureId);

        $this->data['pageTitle'] = __('Forum Topics in').' '.$session->session_name;
        $this->data['id']=$sessionId;


        $this->data['topics'] = $topics;

        if($topics->count()==0){
            $this->data['message'] = __('no-topics');
        }
        $this->data['student'] = $this->getStudent();

        return $this->bladeView('application.forum.topics',$this->data);
        //return $this->data;
    }

    public function addtopicAction(){
        $id = $this->params('id');
        $this->validateAccess($id);
        $form = $this->forumTopicForm();

        if($this->request->isPost()){
            $formData = $this->request->getPost();
            $form->setData($formData);
            if($form->isValid()){
                $data = $form->getData();
                //create topic


                DB::transaction(function() use($data,$id){
                    $studentId= $this->getStudent()->student_id;
                    $forumTopic = ForumTopic::create([
                        'topic_title'=>$data['topic_title'],
                        'created_on'=>time(),
                        'topic_owner'=>$studentId,
                        'topic_owner_type'=>'s',
                        'session_id'=>$id
                    ]);

                    $message = $this->saveInlineImages($data['message'],$this->getBaseUrl());
                    $message = sanitizeHtml($message);

                    //creat post
                    $postId = ForumPost::create([
                        'forum_topic_id'=>$forumTopic->forum_topic_id,
                        'message'=>$message,
                        'post_created_on'=>time(),
                        'post_owner'=>$studentId,
                        'post_owner_type'=>'s',
                    ]);

                    $fpTable = new ForumParticipantTable($this->getServiceLocator());
                    $fpTable->updateParticipant($forumTopic->forum_topic_id,$studentId,'s');
                    //now redirect to topic page
                    return $this->redirect()->toRoute('application/default',['controller'=>'forum','action'=>'topic','id'=>$forumTopic->forum_topic_id]);


                });


   }
            else{
                $this->data['message'] = $this->getFormErrors($form);
            }
        }

        $this->data['pageTitle'] = __('Add Topic').': '.Session::find($id)->session_name;
        $this->data['form'] = $form;
        $this->data['customCrumbs'] = [
            $this->url()->fromRoute('home')=>__('Home'),
            $this->url()->fromRoute('application/dashboard')=>__('Dashboard'),
             $this->url()->fromRoute('application/default',['controller'=>'forum','action'=>'index'])=>__('Student Forum'),
             $this->url()->fromRoute('application/default',['controller'=>'forum','action'=>'topics','id'=>$id])=>__('Forum Topics'),
            '#'=>__('Add Topic')
        ];
        return $this->data;

    }


    public function topicAction(){
        $id = $this->params('id');
        $this->data['id'] = $id;
        $forumTopic = ForumTopic::find($id);
        $sessionId = $forumTopic->session->session_id;
        $this->validateAccess($sessionId);

        $this->data['posts'] = $forumTopic->forumPosts()->paginate(70);
        $this->data['pageTitle'] = $forumTopic->session->session_name;
        $this->data['customCrumbs'] = [
            $this->url()->fromRoute('home')=>__('Home'),
            $this->url()->fromRoute('application/dashboard')=>__('Dashboard'),
            $this->url()->fromRoute('application/default',['controller'=>'forum','action'=>'index'])=>__('Student Forum'),
            $this->url()->fromRoute('application/default',['controller'=>'forum','action'=>'topics','id'=>$sessionId])=>__('Forum Topics'),
            '#'=>__('Forum Topic')
        ];

        $checkbox = new Checkbox('notify');
        $checkbox->setAttribute('id','notify');
        $checkbox->setCheckedValue(1);
        //check for participant
        $participant = ForumParticipant::where('forum_topic_id',$id)->where('user_id',$this->getStudent()->student_id)->where('user_type','s')->first();
        if($participant && $participant->notify==1){
            $checkbox->setChecked(true);
        }
        else{
            $checkbox->setChecked(false);
        }
        $this->data['checkbox'] = $checkbox;
        $this->data['forumTopic'] = $forumTopic;


        return $this->data;
    }

    public function replyAction(){
        ini_set('post_max_size', '5M');
        $id = $this->params('id');
        $topic = ForumTopic::find($id);
        $this->validateAccess($topic->session_id);
        if($this->request->isPost()){
            $post = $this->request->getPost();
            $message = $post['message'];

            if(empty($message)){
                $this->flashMessenger()->addMessage(__('Please enter a message'));
                return $this->goBack();
            }


            $message = $this->saveInlineImages($message,$this->getBaseUrl());

            $student = $this->getStudent();
            $message= sanitizeHtml($message);


            $reply = new ForumPost();
            $reply->forum_topic_id = $id;
            $reply->message = $message;
            $reply->post_created_on = time();
            $reply->post_owner = $student->student_id;
            $reply->post_owner_type = 's';
            if(!empty($post['post_reply_id'])){
                $reply->post_reply_id = $post['post_reply_id'];
            }
            try{
                $reply->save();
            }
            catch(\Exception $ex){
                $this->flashMessenger()->addMessage(__('forum-content-error'));
                flash($post);
                return $this->goBack();
            }



            $fpTable = new ForumParticipantTable($this->getServiceLocator());
            $fpTable->updateParticipant($id,$student->student_id,'s');
            $this->notifyParticipants($id);
            $this->flashMessenger()->addMessage(__('Reply saved!'));

        }

        return $this->goBack();
    }

    public function notificationsAction(){
        $id = $this->params('id');
        $topic = ForumTopic::find($id);
        $this->validateAccess($topic->session_id);

        $notify = $this->request->getQuery('notify');
        $table = new ForumParticipantTable($this->getServiceLocator());
        $table->updateParticipant($id,$this->getStudent()->student_id,'s',$notify);
        exit('true');

    }

    public function deletetopicAction(){
        //check if user is owner of topic
        $id = $this->params('id');
        $forumTopic = ForumTopic::findOrFail($id);

        $student = $this->getStudent();
        if($student->student_id == $forumTopic->topic_owner && $forumTopic->topic_owner_type=='s'){
            $forumTopic->delete();
            $this->flashMessenger()->addMessage(__('Topic deleted!'));

        }

        return $this->goBack();
    }


    private function validateAccess($sessionId){
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $sessionTable = new SessionTable($this->getServiceLocator());
        $session = $sessionTable->getRecord($sessionId);
        if(!$studentSessionTable->enrolled($this->getId(),$sessionId) && $session->enable_forum==1 && $session->session_status==1){
            $this->flashmessenger->addMessage(__('forum-unavailable'));
            return $this->redirect()->toRoute('application/default',['controller'=>'forum','action'=>'index']);
        }
    }
}