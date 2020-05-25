<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 11/22/2018
 * Time: 11:02 AM
 */

namespace Application\Slim\V1\Controller;

use Application\Entity\Account;
use Application\Entity\ForumParticipant;
use Application\Entity\ForumPost;
use Application\Entity\ForumTopic;
use Application\Entity\Lecture;
use Application\Entity\Session;
use Application\Entity\Student;
use Application\Model\ForumParticipantTable;
use Application\Model\ForumTopicTable;
use Application\Model\SessionTable;
use Application\Model\StudentSessionTable;
use Illuminate\Database\Capsule\Manager as DB;
use Intermatics\HelperTrait;
use Intermatics\TraitClasses\ForumTrait;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ForumController  extends Controller {

    use HelperTrait;

    use ForumTrait;

    public function forumSessions(Request $request,Response $response,$args)
    {

        $forumTopicsTable = new ForumTopicTable();
        $params = $request->getQueryParams();

        $page = !empty($params['page']) ? $params['page'] : 1;

        $rowsPerPage = 30;

        $studentSessionTable = new StudentSessionTable();
        $studentId = $this->getApiStudentId();

        $total = $studentSessionTable->getTotalStudentForumRecords($studentId);

        $totalPages = ceil($total / $rowsPerPage);
        $records = [];

        if ($page <= $totalPages) {
            $paginator = $studentSessionTable->getStudentForumRecords(true, $studentId);
            $paginator->setCurrentPageNumber((int)$page);
            $paginator->setItemCountPerPage($rowsPerPage);

            foreach ($paginator as $row) {
                $row->total_topics = Session::find($row->session_id)->forumTopics()->count();
                $records[] = $row;
            }

        }

        return jsonResponse([
            'total_pages' => $totalPages,
            'current_page' => $page,
            'total' => $total,
            'rows_per_page' => $rowsPerPage,
            'records' => $records,
        ]);

    }

    public function forumTopics(Request $request,Response $response,$args){

        $params = $request->getQueryParams();

        $isValid = $this->validate($params,[
           'course_id'=>'required'
        ]);

        if(!$isValid){
            return jsonResponse(['status'=>false,'msg'=>$this->getValidationErrors()]);
        }
        
        $sessionId = $params['course_id'];
        $this->validateAccess($sessionId);

        $lectureId = @$params['lecture_id'];
        $session = Session::find($sessionId);

        if(!empty($lectureId)){
            $topics = $session->forumTopics()->where('lecture_id',$lectureId)->orderBy('forum_topic_id','desc')->paginate(20);

        }
        else{
            $topics = $session->forumTopics()->orderBy('forum_topic_id','desc')->paginate(20);
        }

        $data['lecture'] = Lecture::find($lectureId);

        $data['topics'] = $topics;

        if($topics->count()==0){
            $data['message'] = 'There are currently no topics available';
        }



        $array = $topics->toArray();

        foreach($array['data'] as $key=>$value){

            if($value['topic_owner_type']=='s' && Student::find($value['topic_owner'])){

                $student = Student::find($value['topic_owner']);
                $value['owner'] = [
                    'first_name'=>$student->first_name,
                    'last_name'=>$student->last_name,
                    'picture'=>$student->picture
                ];

                //unset($value['owner']['api_token'],$value['owner']['password'],$value['owner']['token_expires'],$value['owner']['token_expires']);
            }
            elseif($value['topic_owner_type']=='a' && Account::find($value['topic_owner'])){
                $account = Account::find($value['topic_owner']);

                $value['owner'] = [
                    'first_name'=>$account->first_name,
                    'last_name'=>$account->last_name,
                    'picture'=>$account->picture
                ];
            }
            else{
                $value['owner'] = null;
            }

            if($value['topic_owner_type']=='s' && $this->getApiStudentId()==$value['topic_owner']){
                $value['can_delete']= true;
            }
            else{
                $value['can_delete']= false;
            }

            $value['total_posts'] = ForumTopic::find($value['forum_topic_id'])->forumPosts()->count();

            $array['data'][$key] = $value;
        }



        return jsonResponse($array);
        
    }

    private function validateAccess($sessionId){
        $studentSessionTable = new StudentSessionTable();
        $sessionTable = new SessionTable();
        $session = $sessionTable->getRecord($sessionId);
        if(!$studentSessionTable->enrolled($this->getApiStudentId(),$sessionId) && $session->enable_forum==1 && $session->session_status==1){

             return jsonResponse([
               'status'=>false,
                'msg'=>'Forum is unavailable for this session/course'
            ]);

        }
    }

    public function getForumTopic(Request $request,Response $response,$args){
        $id = $args['id'];

        $output =  [];
        $output['details'] = ForumTopic::find($id);
        $output['total_posts']= ForumTopic::find($id)->forumPosts()->count();

        return jsonResponse($output);

    }

    public function createForumTopic(Request $request,Response $response,$args){
        $data = $request->getParsedBody();

        $isValid= $this->validate($data,[
           'topic_title'=>'required',
           'message'=>'required',
           'session_id'=>'required'
        ]);

        if(!$isValid){
            return jsonResponse(['status'=>false,'msg'=>$this->getValidationErrors()]);
        }

        $this->validateAccess($data['session_id']);

        $id = $data['session_id'];

        DB::transaction(function() use ($data,$id,$request){

            $studentId= $this->getApiStudentId();
            $forumTopic = ForumTopic::create([
                'topic_title'=>$data['topic_title'],
                'created_on'=>time(),
                'topic_owner'=>$studentId,
                'topic_owner_type'=>'s',
                'session_id'=>$id
            ]);



            $message = $data['message'];

            $post = ForumPost::create([
                'forum_topic_id'=>$forumTopic->forum_topic_id,
                'message'=>$message,
                'post_created_on'=>time(),
                'post_owner'=>$studentId,
                'post_owner_type'=>'s',
            ]);

            $fpTable = new ForumParticipantTable();
            $fpTable->updateParticipant($forumTopic->forum_topic_id,$studentId,'s');



        });

        return jsonResponse([
            'status'=>true,
   //         'topic'=> $forumTopic,
   //         'post'=>$post
        ]);



    }

    public function deleteForumTopic(Request $request,Response $response,$args){

        $id = $args['id'];
        $forumTopic = ForumTopic::findOrFail($id);

        $student = $this->getApiStudent();
        if($student->student_id == $forumTopic->topic_owner && $forumTopic->topic_owner_type=='s'){
            $forumTopic->delete();
            return jsonResponse([
                'status'=>true
            ]);
        }

    }

    public function getForumPosts(Request $request,Response $response,$args){

        $params = $request->getQueryParams();


        $this->validateParams($params,[
           'forum_topic_id'=>'required'
        ]);


        $id = $params['forum_topic_id'];

        $forumTopic = ForumTopic::find($id);
        $sessionId = $forumTopic->session->session_id;
        $this->validateAccess($sessionId);

        $data = [];
        $data['status'] = true;
        $data['posts'] = $forumTopic->forumPosts()->paginate(70)->toArray();


        foreach($data['posts']['data'] as $key=>$value){

            if($value['post_owner_type']=='s' && Student::find($value['post_owner'])){

                $student = Student::find($value['post_owner']);
                $owner = [
                    'first_name'=>$student->first_name,
                    'last_name'=>$student->last_name,
                    'picture'=>$student->picture
                ];

                //unset($value['owner']['api_token'],$value['owner']['password'],$value['owner']['token_expires'],$value['owner']['token_expires']);
            }
            elseif($value['post_owner_type']=='a' && Account::find($value['post_owner'])){
                $account = Account::find($value['post_owner']);

                $owner = [
                    'first_name'=>$account->first_name,
                    'last_name'=>$account->last_name,
                    'picture'=>$account->picture
                ];
            }
            else{
                $owner = null;
            }


            $data['posts']['data'][$key]['owner'] = $owner;
        }

        //check for participant
        $participant = ForumParticipant::where('forum_topic_id',$id)->where('user_id',$this->getApiStudent()->student_id)->where('user_type','s')->first();

        $data['forum_topic'] = [
         'topic_title'=>$forumTopic->topic_title,
            'topic_owner'=>$forumTopic->topic_owner,
            'forum_topic_id'=>$forumTopic->forum_topic_id,
            'session_id'=>$forumTopic->session_id,
            'session_name'=>$forumTopic->session->session_name
        ];


        return jsonResponse($data);



    }

    public function createForumPost(Request $request,Response $response,$args)
    {
        $post = $request->getParsedBody();

        $this->validateParams($post, [
            'forum_topic_id' => 'required',
            'message' => 'required'
        ]);

        ini_set('post_max_size', '5M');
        $id = $post['forum_topic_id'];
        $topic = ForumTopic::find($id);
        $this->validateAccess($topic->session_id);


        $message = nl2br($post['message']);

        $student = $this->getApiStudent();
        $reply = new ForumPost();
        $reply->forum_topic_id = $id;
        $reply->message = $message;
        $reply->post_created_on = time();
        $reply->post_owner = $student->student_id;
        $reply->post_owner_type = 's';
        if (!empty($post['post_reply_id'])) {
            $reply->post_reply_id = $post['post_reply_id'];
        }
        try {
            $reply->save();
        } catch (\Exception $ex) {

            return jsonResponse([
                'status' => false,
                'msg' => 'An error occurred.'
            ]);
        }


        $fpTable = new ForumParticipantTable();
        $fpTable->updateParticipant($id, $student->student_id, 's');
        $this->notifyParticipants($id,true,$this->getBaseApiUrl($request));

        return jsonResponse([
            'status' => true,
            'msg' => 'Reply saved!',
            'reply'=>$reply
        ]);

    }


}