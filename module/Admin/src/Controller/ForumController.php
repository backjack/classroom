<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 8/22/2017
 * Time: 11:11 AM
 */

namespace Admin\Controller;

use Application\Controller\AbstractController;
use Application\Entity\ForumParticipant;
use Application\Entity\ForumPost;
use Application\Entity\ForumTopic;
use Application\Entity\Lecture;
use Application\Entity\Session;
use Application\Entity\SessionInstructor;
use Application\Model\ForumParticipantTable;
use Application\Model\ForumTopicTable;
use Application\Model\SessionInstructorTable;
use Application\Model\SessionTable;
use Application\Model\StudentSessionTable;
use Intermatics\BaseForm;
use Intermatics\TraitClasses\ForumTrait;
use Intermatics\HelperTrait;
use Zend\EventManager\EventManagerInterface;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Select;
use Zend\InputFilter\InputFilter;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Illuminate\Database\Capsule\Manager as DB;


class ForumController extends AbstractController {

    use HelperTrait;
    use ForumTrait;





    /**
     * Get list of all sessions student is enrolled in and for which forum is enabled
     */
    public function indexAction(){

        $table = new ForumTopicTable($this->getServiceLocator());

        $sessionId = $this->request->getQuery('session_id');

        $paginator = $table->getTopicsForAdmin($this->getAdminId(),$sessionId);
        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);

        $pageTitle = __('Student Forum').': '.$table->total.' '.__('topics');
        if(!empty($sessionId)){
            $pageTitle = __('Forum Topics for').' '.Session::find($sessionId)->session_name.' ('.$table->total.')';
        }

        $form = $this->adminForumForm();
        $form->get('session_id')->setValue($sessionId);
        $form->get('session_id')->setAttribute('style','min-width:150px');
        return [
            'topics'=>$paginator,
            'pageTitle'=>$pageTitle,
            'select'=> $form->get('session_id')
        ];

    }



    public function addtopicAction(){

        $form = $this->adminForumForm();

        if($this->request->isPost()){
            $formData = $this->request->getPost();
            $form->setData($formData);
            if($form->isValid()){
                $data = $form->getData();
                //create topic


                DB::transaction(function() use($data){

                    $forumTopic = ForumTopic::create([
                        'topic_title'=>$data['topic_title'],
                        'created_on'=>time(),
                        'topic_owner'=>$this->getAdminId(),
                        'topic_owner_type'=>'a',
                        'session_id'=>$data['session_id']
                    ]);

                    $message = $this->saveInlineImages($data['message'],$this->getBaseUrl());
                    $message = sanitizeHtml($message);

                    //creat post
                    $postId = ForumPost::create([
                        'forum_topic_id'=>$forumTopic->forum_topic_id,
                        'message'=>$message,
                        'post_created_on'=>time(),
                        'post_owner'=>$this->getAdminId(),
                        'post_owner_type'=>'a',
                    ]);

                    $fpTable = new ForumParticipantTable($this->getServiceLocator());
                    $fpTable->updateParticipant($forumTopic->forum_topic_id,$this->getAdminId(),'a');
                    //now redirect to topic page
                    return $this->redirect()->toRoute('admin/default',['controller'=>'forum','action'=>'topic','id'=>$forumTopic->forum_topic_id]);


                });


   }
            else{
                $this->data['message'] = $this->getFormErrors($form);
            }
        }

        $this->data['pageTitle'] = __('Add Topic');
        $this->data['form'] = $form;
        $this->data['customCrumbs'] = [
            $this->url()->fromRoute('home')=>__('Home'),
            $this->url()->fromRoute('application/dashboard')=>__('Dashboard'),
             $this->url()->fromRoute('admin/default',['controller'=>'forum','action'=>'index'])=>__('Student Forum'),
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
            $this->url()->fromRoute('admin/default',['controller'=>'index','action'=>'index'])=>__('Dashboard'),
            $this->url()->fromRoute('admin/default',['controller'=>'forum','action'=>'index'])=>__('Student Forum'),
            '#'=>__('Forum Topic')
        ];

        $checkbox = new Checkbox('notify');
        $checkbox->setAttribute('id','notify');
        $checkbox->setCheckedValue(1);
        //check for participant
        $participant = ForumParticipant::where('forum_topic_id',$id)->where('user_id',$this->getAdminId())->where('user_type','a')->first();
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

            $message= sanitizeHtml($message);

            $reply = new ForumPost();
            $reply->forum_topic_id = $id;
            $reply->message = $message;
            $reply->post_created_on = time();
            $reply->post_owner = $this->getAdminId();
            $reply->post_owner_type = 'a';
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
            $fpTable->updateParticipant($id,$this->getAdminId(),'a');
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
        $table->updateParticipant($id,$this->getAdminId(),'a',$notify);
        exit('true');

    }

    public function deletetopicAction(){
        $id = $this->params('id');
        $forumTopic = ForumTopic::findOrFail($id);
        $this->validateAccess($forumTopic->session_id);
        $forumTopic->delete();
        $this->flashMessenger()->addMessage(__('Topic deleted'));
        return $this->goBack();

    }


    private function validateAccess($sessionId){

        //check if user has global access
        if($this->hasPermission('misc/global_access')){
            return true;
        }

        //check if is owner of session
        if(Session::find($sessionId)->account_id==$this->getAdminId()){
            return true;
        }
        $sessionInstructorTable = new SessionInstructorTable($this->getServiceLocator());
        //check if is instructor
        if($sessionInstructorTable->isInstructor($sessionId,$this->getAdminId())){
            return true;
        }


            $this->flashmessenger->addMessage(__('no-forum-access'));
            return $this->redirect()->toRoute('admin/default',['controller'=>'forum','action'=>'index']);

    }

    private function adminForumForm(){
        $form = new BaseForm();
        $form->createText('topic_title',__('Topic'),true,null,null,__('enter-thread-topic'));
        $form->createTextArea('message',__('Post'),true,null,__('enter-first-post'));
        $form->get('message')->setAttribute('class','form-control summernote');


        $sessionTable = new SessionTable($this->getServiceLocator());
        $rowset = $sessionTable->getLimitedRecords(5000);




        $options = [''=>''];
        $log = [];
        foreach($rowset as $row){
            // $options[$row->session_id] = $row->session_name;
            $options[] =  ['attributes'=>['data-type'=>$row->session_type],'value'=>$row->session_id,'label'=>$row->session_name.' ('.$row->session_id.')'];
            $log[$row->session_id]=true;
        }
        $sessionInstructorTable = new SessionInstructorTable($this->getServiceLocator());
        $rowset = $sessionInstructorTable->getAccountRecords($this->getAdminId());
        foreach($rowset as $row){
            if(isset($log[$row->session_id])){
                continue;
            }
            // $options[$row->session_id] = $row->session_name;
            $options[] =  ['attributes'=>['data-type'=>$row->session_type],'value'=>$row->session_id,'label'=>$row->session_name.' ('.$row->session_id.')'];

        }

        //$form->createSelect('session_id','Session/Course',$options,true);
        // $form->get('session_id')->setAttribute('class','form-control select2');

        $sessionId = new Select('session_id');
        $sessionId->setLabel(__('Session/Course'));
        $sessionId->setAttribute('class','form-control select2');
        $sessionId->setAttribute('id','session_id');
        $sessionId->setValueOptions($options);

        $form->add($sessionId);

        $form->setInputFilter($this->adminForumFilter());
        return $form;
    }

    private function adminForumFilter(){
        $filter = $this->forumTopicFilter();
        $filter->add([
            'name'=>'session_id',
            'required'=>true,
            'validators'=>[
                [
                    'name'=>'NotEmpty'
                ]
            ]
        ]);
        return $filter;
    }

}