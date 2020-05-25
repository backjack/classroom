<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 2/9/2017
 * Time: 10:18 AM
 */

namespace Admin\Controller;



use Application\Controller\AbstractController;
use Application\Model\AccountsTable;
use Application\Model\DiscussionAccountTable;
use Application\Model\DiscussionReplyTable;
use Application\Model\DiscussionTable;
use Application\Model\StudentTable;
use Application\Model\StudentTestTable;
use Intermatics\HelperTrait;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DiscussController extends AbstractController {

    use HelperTrait;
    public function indexAction() {
        // TODO Auto-generated ArticlesController::indexAction() default action
        $table = new DiscussionTable($this->getServiceLocator());
        $discussionReplyTable = new DiscussionReplyTable($this->getServiceLocator());
        $discussionAccountTable = new DiscussionAccountTable($this->getServiceLocator());

        //$replied = $this->request->getQuery('replied');
        $replied= @$_GET['replied'];
        $total = $table->getTotalDiscussions($replied);

        $paginator = $table->getDiscussRecords(true,$replied);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        return new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Instructor Chat'),
            'replyTable'=>$discussionReplyTable,
            'total'=>$total,
            'accountTable'=>$discussionAccountTable,
        ));

    }


    public function addreplyAction(){
        $table = new DiscussionReplyTable($this->getServiceLocator());
        $discussionTable = new DiscussionTable($this->getServiceLocator());
        $studentTable = new StudentTable($this->getServiceLocator());

        $accountTable = new AccountsTable($this->getServiceLocator());
        $id = $this->params('id');
        $this->validateDiscussion($id);
        $discussionRow = $discussionTable->getRecord($id);

        if($this->request->isPost())
        {

            $reply = $this->request->getPost('reply');
            $user= $this->getAdmin();

            if(!empty($reply)){
                $data = [
                    'discussion_id'=>$id,
                    'reply'=> $reply,
                    'replied_on'=> time(),
                    'user_id'=> $user->account_id,
                    'user_type'=>'a'
                ];
                $rid= $table->addRecord($data);

                if(!empty($rid))
                {
                    //update discussion
                    $discussionTable->update(['replied'=>1],$id);
                }

                $name = $user->first_name.' '.$user->last_name;
                //send notification to admins
                $subject = __('New reply for').' "'.$discussionRow->subject.'"';
                $message = __('discussion-reply-mail',['subject'=>$discussionRow->subject,'name'=>$name,'reply'=>$reply]);
                $studentLink= $this->getBaseUrl().'/signin';
                $studentLink = "<a href=\"$studentLink\" >$studentLink</a>";
                $adminLink = $this->getBaseUrl().'/admin';
                $adminLink = "<a href=\"$adminLink\" >$adminLink</a>";
                //notify student
                $student = $studentTable->getRecord($discussionRow->student_id);
                $this->sendEmail($student->email,$subject,$message.$studentLink);

                $rowset = $table->getRepliedAdmins($id);
                foreach($rowset as $row){
                    try{
                        $account = $accountTable->getRecord($row->user_id);
                        if(!empty($account->email) && $account->email != $user->email){
                            $this->sendEmail($account->email,$subject,$message.$adminLink);
                        }
                    }
                    catch(\Exception $ex)
                    {

                    }

                }

                $this->flashMessenger()->addMessage(__('reply-added-msg'));
            }
            else{
                $this->flashMessenger()->addMessage(__('submission-failed-msg'));
            }

        }

        $this->redirect()->toRoute('admin/default',['controller'=>'discuss','action'=>'index']);
    }

    public function viewdiscussionAction()
    {
        $table = new DiscussionReplyTable($this->getServiceLocator());
        $discussionTable = new DiscussionTable($this->getServiceLocator());
        $discussionAccountTable =  new DiscussionAccountTable($this->getServiceLocator());
        $id = $this->params('id');
        $this->validateDiscussion($id);
        $row= $discussionTable->getRecord($id);


        $paginator = $table->getPaginatedRecordsForDiscussion(true,$id);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        $accounts = $discussionAccountTable->getDiscussionAccounts($id);

        return new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>__('View Chat'),
            'row'=>$row,
            'studentTable'=>new StudentTable($this->getServiceLocator()),
            'accountTable'=> new AccountsTable($this->getServiceLocator()),
            'total'=>$table->getTotalReplies($id),
            'accounts'=>$accounts
        ));
    }

    public function deleteAction()
    {
        $table = new DiscussionTable($this->getServiceLocator());
        $discussionAccountTable = new DiscussionAccountTable($this->getServiceLocator());
        $id = $this->params('id');
        $this->validateDiscussion($id);

        if(GLOBAL_ACCESS){
            $table->deleteRecord($id);
        }
        else{
            $discussionAccountTable->deleteAccountRecord($id,ADMIN_ID);
        }


        $this->flashmessenger()->addMessage(__('Record deleted'));
        $this->redirect()->toRoute('admin/default',array('controller'=>'discuss','action'=>'index'));
    }


    private function validateDiscussion($id){

        $discussionAccountTable = new DiscussionAccountTable($this->getServiceLocator());
        if($discussionAccountTable->hasDiscussion(ADMIN_ID,$id) || GLOBAL_ACCESS){
            return true;
        }
        else{
            return $this->goBack();
        }
    }

}