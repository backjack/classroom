<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 11/22/2018
 * Time: 11:01 AM
 */

namespace Application\Slim\V1\Controller;

use Application\Entity\Account;
use Application\Entity\Discussion;
use Application\Entity\Student;
use Application\Model\AccountsTable;
use Application\Model\DiscussionAccountTable;
use Application\Model\DiscussionReplyTable;
use Application\Model\DiscussionTable;
use Application\Model\SessionTable;
use Application\Model\StudentSessionTable;
use Intermatics\HelperTrait;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class DiscussionController extends Controller  {

    use HelperTrait;

    public function options(Request $request,Response $response,$args){
        //get list of recipients
        $studentSessionTable = new StudentSessionTable();
        $rowset = $studentSessionTable->getSessionInstructors($this->getApiStudentId());
        $recipients = [];
      //  $recipients[]['admins'] = 'Administrators';
        $recipients[] = [
            'id'=>'admins',
            'name'=>'Administrators'
        ];

        $accounts= [];
        foreach($rowset as $row){
            if(!empty($row->enable_discussion)){
                $accounts[$row->account_id]= $row->first_name.' '.$row->last_name.' ('.$row->session_name.')';


            }

        }

        foreach($accounts as $key=>$value){
            $recipients[] = [
                'id'=>$key,
                'name'=>$value
            ];
        }

        //get list of sessions
        $rowset = $studentSessionTable->getStudentRecords(false,$this->getApiStudentId());
        $sessions = [];
        foreach($rowset as $row){
            if(!empty($row->enable_discussion)){
              //  $sessions[$row->session_id] = $row->session_name;
                $sessions[]=[
                  'id'=>  $row->session_id,
                    'name'=>$row->session_name
                ];
            }

        }

        return jsonResponse(
            [
                'recipients'=>$recipients,
                'courses'=>$sessions
            ]
        );


    }

    public function discussions(Request $request,Response $response,$args){

        $params = $request->getQueryParams();

        $lectureId = empty($params['lecture_id'])? null:$params['lecture_id'];
        $sessionId = empty($params['session_id'])? null:$params['session_id'];

        $table = new DiscussionTable();
        $discussionAccountTable = new DiscussionAccountTable();
        $sessionTable = new SessionTable();

        $query = Student::find($this->getApiStudentId())->discussions()->orderBy('discussion_id','desc');

        if(!empty($lectureId)){
            $query->where('lecture_id',$lectureId);
        }

        if(!empty($sessionId)){
            $query->where('session_id',$sessionId);
        }

        $rowset = $query->paginate(30);

        $output = $rowset->toArray();

        foreach($output['data'] as $key=>$value){

            //get the id
            $id = $value['discussion_id'];
            //get accounts for it
            $accounts = $discussionAccountTable->getDiscussionAccounts($id)->toArray();
            foreach($accounts as $key1=>$value1){
                $accounts[$key1]['picture'] = Account::find($value1['account_id'])->picture;
                unset($accounts[$key1]['email']);
            }

            $output['data'][$key]['recipients'] = $accounts;

            $output['data'][$key]['replies'] = Discussion::find($id)->discussionReplies()->count();
        }

        return jsonResponse($output);




    }

    public function getDiscussion(Request $request,Response $response,$args){

        $id= $args['id'];

        $discussion = Discussion::find($id);

        $output = [];
        $discussionAccountTable = new DiscussionAccountTable();
        $accounts = $discussionAccountTable->getDiscussionAccounts($id)->toArray();

        foreach($accounts as $key=>$value){
            $accounts[$key]['picture'] = Account::find($value['account_id'])->picture;
            unset($accounts[$key]['email']);
        }

        $output['details'] = $discussion->toArray();

        //get student info
        $output['details']['student'] = Student::find($discussion->student_id)->toArray();

        $output['recipients'] = $accounts;

        //get replies
        $replies = $discussion->discussionReplies()->orderBy('discussion_reply_id','asc')->paginate(70);

        $replies = $replies->toArray();

        foreach($replies['data'] as $key=>$value){

            if($value['user_type']=='s' && Student::find($value['user_id'])){

                $student = Student::find($value['user_id']);
                $owner = [
                    'first_name'=>$student->first_name,
                    'last_name'=>$student->last_name,
                    'picture'=>$student->picture
                ];

                //unset($value['owner']['api_token'],$value['owner']['password'],$value['owner']['token_expires'],$value['owner']['token_expires']);
            }
            elseif($value['user_type']=='a' && Account::find($value['user_id'])){
                $account = Account::find($value['user_id']);

                $owner = [
                    'first_name'=>$account->first_name,
                    'last_name'=>$account->last_name,
                    'picture'=>$account->picture
                ];
            }
            else{
                $owner = null;
            }


            $replies['data'][$key]['owner'] = $owner;
        }



        $output['replies'] = $replies;

        return jsonResponse($output);

    }

    public function createDiscussionReply(Request $request,Response $response,$args){

        $data = $request->getParsedBody();

        $this->validateParams($data,[
            'discussion_id'=>'required',
            'reply'=>'required'
        ]);

        $table = new DiscussionReplyTable();
        $discussionTable = new DiscussionTable();
        $accountTable = new AccountsTable();
        
        $id = $data['discussion_id'];

        $discussionRow = $discussionTable->getRecord($id);
        $this->validateApiOwner($discussionRow);
        
        $data['replied_on'] = time();
        $data['user_id']= $this->getApiStudentId();
        $data['user_type'] = 's';

        $table->addRecord($data);
        $discussionTable->update(['replied'=>0],$id);
        $user = $this->getApiStudent();
        $name = $user->first_name.' '.$user->last_name;
        $reply = $data['reply'];
        //send notification to admins
        $subject = 'New reply for "'.$discussionRow->subject.'"';
        $message = 'New reply for "'.$discussionRow->subject."\". $name said: <br/>".$reply;
        $rowset = $table->getRepliedAdmins($id);
        foreach($rowset as $row){
            try{
                $account = $accountTable->getRecord($row->user_id);
                if(!empty($account->email)){
                    $this->sendEmail($account->email,$subject,$message);
                }
            }
            catch(\Exception $ex)
            {

            }

        }

        return jsonResponse([
            'status'=>true,
            'msg'=>'Reply saved'
        ]);
        

    }

    public function createDiscussion(Request $request,Response $response,$args){

        $data = $request->getParsedBody();

        $this->validateParams($data,[
           'subject'=>'required',
            'question'=> 'required'
        ]);

        $discussionTable = new DiscussionTable($this->getServiceLocator());
        $discussionAccountTable = new DiscussionAccountTable($this->getServiceLocator());
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());

        $data = removeTags($data);
        $data['created_on'] = time();
        $data['student_id'] = $this->getApiStudentId();


        if(isset($data['accounts'])){
            $accounts = $data['accounts'];
            unset($data['accounts']);
        }
        else{
            $accounts = [];
        }

        $discussionId = $discussionTable->addRecord($data);

        $title = 'New question: '.$data['subject'];
        $user = $this->getApiStudent();

        //get list of sessions
        $list = '<br/><strong>Enrolled Sessions/Courses</strong>:';
        if($studentSessionTable->getTotalForStudent($this->getApiStudentId())==0){
            $list .= 'None';
        }
        else{
            $rowset = $studentSessionTable->getStudentRecords(false,$this->getApiStudentId());
            foreach($rowset as $row){
                $list.=$row->session_name.', ';
            }

        }
        $list= '<br/>';
        $message = $user->first_name.' '.$user->last_name.' has asked a question.<br/><strong>Subject:'.$data['subject'].'</strong><br/><p>'.$data['question'].'</p>'.$list.'<br/><br/><strong>Please do not reply to this email!</strong> <a href="'.$this->getBaseApiUrl($request).'/admin'.'" >Login</a> to your account to answer this question.';

        $admins = 0;

        foreach($accounts as $value){
            $accountId = $value[0];
            if($accountId !='admins'){

                $this->notifyAdmin($accountId,$title,$message);
                $discussionAccountTable->addRecord([
                    'account_id'=>$accountId,
                    'discussion_id'=> $discussionId
                ]);
            }
            else{
                $admins = 1;
                $this->notifyAdmins($title,$message);
            }
        }
        $discussionTable->update(['admin'=>$admins],$discussionId);

        return jsonResponse([
            'status'=>true
        ]);


    }

    public function deleteDiscussion(Request $request,Response $response,$args){
        $id = $args['id'];

        $row = Discussion::find($id);
        $this->validateApiOwner($row);

        $row->delete();
        return jsonResponse([
           'status'=>true
        ]);

    }

}