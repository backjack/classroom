<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 8/10/2016
 * Time: 8:39 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class StudentSessionTable extends BaseTable {

    protected $tableName = 'student_session';
    protected $primary = 'student_session_id';

    public function getTotalDistinctStudents(){

        $select = new Select($this->tableName);
        $select->columns(array(new Expression('DISTINCT(student_id) as id')));


        $rowset = $this->tableGateway->selectWith($select);
        $total = $rowset->count();
        return $total;
    }

    public function getTotalActiveStudents(){
        $timeLimit = time() - (86400 * 30);
        $select = new Select($this->tableName);
        $select->join('student',$this->tableName.'.student_id=student.student_id',['last_seen']);
        $select->where('last_seen >= '.$timeLimit);
        $select->columns(array(new Expression('DISTINCT(student_session.student_id) as id')));


        $rowset = $this->tableGateway->selectWith($select);
        $total = $rowset->count();
        return $total;
    }


    public function getActiveStudents()
    {
        $timeLimit = time() - (86400 * 30);
        $select = new Select($this->tableName);
        $select->join('student',$this->tableName.'.student_id=student.student_id',['last_seen','first_name','last_name','email']);
        $select->where('last_seen >= '.$timeLimit);
        $select->group('student_id');

        $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
        $paginator = new Paginator($paginatorAdapter);
        return $paginator;
    }


    public function getRecord($id){

        $select = new Select($this->tableName);
        $select->where([$this->primary=>$id])
                ->join('student',$this->tableName.'.student_id=student.student_id')
                ->join('session',$this->tableName.'.session_id=session.session_id');
        $row = $this->tableGateway->selectWith($select)->current();
        return $row;
   }

    public function getTotalForSession($id){
        $select = new Select($this->tableName);
        $select->where(array('session_id'=>$id));
        $rowset = $this->tableGateway->selectWith($select);
        $total = $rowset->count();
        return $total;
    }

    public function getTotalForStudent($id){
        $select = new Select($this->tableName);
        $select->where(array('student_id'=>$id));
        $rowset = $this->tableGateway->selectWith($select);
        $total = $rowset->count();
        return $total;
    }

    public function enrolled($studentId,$sessionId){
        if(empty($studentId)){
            return false;
        }
        $select = new Select($this->tableName);
        $select->where(array('student_id'=>$studentId,'session_id'=>$sessionId));
        $rowset = $this->tableGateway->selectWith($select);

        $total = $rowset->count();
        if(empty($total)){
            return false;
        }
        else{
            return true;
        }
    }

    public function unenroll($studentId,$sessionId){
        $select = new Select($this->tableName);
        $total = $this->tableGateway->delete(array('student_id'=>$studentId,'session_id'=>$sessionId));

        return $total;
    }

    public function addRecord($data){

        //check for limit
        if(defined('STUDENT_LIMIT') &&  STUDENT_LIMIT > 0 ){
            $enrolled = $this->getTotalActiveStudents();
            if($enrolled >= STUDENT_LIMIT){
                return false;
            }
        }

        $select = new Select($this->tableName);
        $select->where(array('student_id'=>$data['student_id'],'session_id'=>$data['session_id']));
        $rowset = $this->tableGateway->selectWith($select);
        $data['enrolled_on'] = time();
        $total = $rowset->count();

        if(empty($total)){
            $this->tableGateway->insert($data);

        }
    }

    public function getSessionRecords($paginated=false,$id,$alpha=false)
    {
        $select = new Select($this->tableName);
        if($alpha){
            $select->order('student.first_name asc');
        }
        else{
            $select->order($this->primary.' desc');
        }

        $select->where(array('student_session.session_id'=>$id))
            ->join('session','student_session.session_id=session.session_id',array('session_name'))
            ->join('student','student_session.student_id=student.student_id',array('first_name','last_name','email','mobile_number'));


        if($paginated)
        {
            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getCertificates($paginated=false,$studentId)
    {
        $select = new Select($this->tableName);

        $select->where(array('student_session.student_id'=>$studentId,'certificate.status'=>1))
            ->join('certificate','student_session.session_id=certificate.session_id',['certificate_name','certificate_id','orientation','description'],'left')
            ->join('session','student_session.session_id=session.session_id',array('session_name'));

        if($paginated)
        {
            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }


    public function getTotalCertificates($studentId)
    {
        $select = new Select($this->tableName);

        $select->where(array('student_session.student_id'=>$studentId,'certificate.status'=>1))
            ->join('certificate','student_session.session_id=certificate.session_id',['certificate_name','certificate_id','orientation','description'],'left')
            ->join('session','student_session.session_id=session.session_id',array('session_name'));


        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }


    public function getForumTopics($paginated=false,$studentId)
    {

        $select = new Select($this->tableName);

        $select->where(array('student_session.student_id'=>$studentId,'forum_topic_id > 0'))
            ->join('forum_topic','student_session.session_id=forum_topic.session_id',['forum_topic_id','topic_title','forum_created_on'=>'created_on','topic_owner','topic_owner_type'],'left')
            ->join('session','student_session.session_id=session.session_id',array('session_name','session_type'));

        if($paginated)
        {
            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }


    public function hasCertificate($studentId,$certificateId){

        $select = new Select($this->tableName);

        $select->where(array('student_session.student_id'=>$studentId,'certificate_id'=>$certificateId))
            ->join('certificate','student_session.session_id=certificate.session_id',null,'left');
        $resultSet = $this->tableGateway->selectWith($select);
        $total = $resultSet->count();

        if(empty($total)){
         return false;
        }
        else{
            return true;
        }

    }

    public function getStudentRecords($paginated=false,$id)
    {
        $select = new Select($this->tableName);
        $select->order($this->primary.' desc');
        $select->where(array('student_id'=>$id))
            ->join('session',$this->tableName.'.session_id=session.session_id',array('session_name','session_date','session_status','session_end_date','description','venue','enrollment_closes','enable_discussion','session_type','short_description','picture','amount')) ;


        if($paginated)
        {
            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }


    public function getStudentForumRecords($paginated=false,$id)
    {
        $select = new Select($this->tableName);
        $select->order($this->primary.' desc');
        $select->where(array('student_id'=>$id,'enable_forum'=>1,'session.session_status'=>1))
            ->join('session',$this->tableName.'.session_id=session.session_id',array('session_name','session_date','session_status','session_end_date','description','venue','enrollment_closes','enable_discussion','session_type','picture')) ;


        if($paginated)
        {
            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getTotalStudentForumRecords($id)
    {
        $select = new Select($this->tableName);
        $select->order($this->primary.' desc');
        $select->where(array('student_id'=>$id,'enable_forum'=>1,'session.session_status'=>1))
            ->join('session',$this->tableName.'.session_id=session.session_id',array('session_name','session_date','session_status','session_end_date','description','venue','enrollment_closes','enable_discussion','session_type')) ;


        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }


    public function getSessionInstructors($id){

        $select = new Select($this->tableName);
        $select->order('accounts.first_name asc');
        $select->where(array('student_id'=>$id))
            ->join('session_instructor',$this->tableName.'.session_id=session_instructor.session_id',array('account_id'))
            ->join('accounts','session_instructor.account_id=accounts.account_id',array('first_name','last_name','email','account_description','picture'))
            ->join('session',$this->tableName.'.session_id=session.session_id',array('session_name','enable_discussion')) ;

       // $select->group('session_instructor.account_id');
        $select->where(['accounts.account_status'=>1]);
        $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
        $paginator = new Paginator($paginatorAdapter);
        return $paginator;

    }

    public function getAssignments($id){

        $select = new Select($this->tableName);

        $now = time();

        $select->order('assignment.created_on desc');
        $select->where(array('student_session.student_id'=>$id,"(due_date > $now OR allow_late='1')",'opening_date < '.time(),'schedule_type'=>'s'))
            ->join('assignment',$this->tableName.'.session_id=assignment.session_id',array('title','instruction','allow_late','due_date','created_on','assignment_type','passmark','account_id','assignment_id'))
            ->join('session',$this->tableName.'.session_id=session.session_id',array('session_name'))
            ->columns([]);


        $select2 = new Select('attendance');
        $select2->where(array('attendance.student_id'=>$id,'assignment.schedule_type'=>'c'))
            ->join('assignment','attendance.lesson_id=assignment.lesson_id AND attendance.session_id=assignment.session_id',['title','instruction','allow_late','due_date','created_on','assignment_type','passmark','account_id','assignment_id'])
            ->join('session','attendance.session_id=session.session_id',array('session_name'))
            ->columns([]);


        $select->combine($select2);


            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;

    }

    public function getTotalAssignments($id){

   /*     $select = new Select($this->tableName);
        $select->order('due_date desc');
        $now = time();
        $select->where(array('student_id'=>$id,"(due_date > $now OR allow_late='1')",'opening_date < '.time(),'schedule_type'=>'s'))
            ->join('assignment',$this->tableName.'.session_id=assignment.session_id',array('title','instruction','due_date','created_on','assignment_type','passmark','account_id','assignment_id'))
            ->join('session',$this->tableName.'.session_id=session.session_id',array('session_name')) ;*/

        $now = time();
        $select = new Select($this->tableName);
        $select->order('assignment.created_on desc');
        $select->where(array('student_session.student_id'=>$id,"(due_date > $now OR allow_late='1')",'opening_date < '.time(),'schedule_type'=>'s'))
            ->join('assignment',$this->tableName.'.session_id=assignment.session_id',array('title','instruction','allow_late','due_date','created_on','assignment_type','passmark','account_id','assignment_id'))
            ->join('session',$this->tableName.'.session_id=session.session_id',array('session_name'))
            ->columns([]);


        $select2 = new Select('attendance');
        $select2->where(array('attendance.student_id'=>$id,'assignment.schedule_type'=>'c'))
            ->join('assignment','attendance.lesson_id=assignment.lesson_id AND attendance.session_id=assignment.session_id',['title','instruction','allow_late','due_date','created_on','assignment_type','passmark','account_id','assignment_id'])
            ->join('session','attendance.session_id=session.session_id',array('session_name'))
            ->columns([]);


        $select->combine($select2);



        $total = $this->tableGateway->selectWith($select)->count();
        return $total;

    }

    public function getDownloads($id){

        $select = new Select($this->tableName);
        $select->order('download_session_id desc');
        $select->where(array('student_id'=>$id,'download.status'=>1))
            ->join('download_session',$this->tableName.'.session_id=download_session.session_id',[])
            ->join('session',$this->tableName.'.session_id=session.session_id',array('session_name'))
            ->join('download','download_session.download_id=download.download_id',array('download_id','download_name'));

        $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
        $paginator = new Paginator($paginatorAdapter);
        return $paginator;

    }

    public function getDownloadsTotal($id){

        $select = new Select($this->tableName);
        $select->order('download_session_id desc');
        $select->where(array('student_id'=>$id,'download.status'=>1))
            ->join('download_session',$this->tableName.'.session_id=download_session.session_id',[])
            ->join('session',$this->tableName.'.session_id=session.session_id',array('session_name'))
            ->join('download','download_session.download_id=download.download_id',array('download_id','download_name'));


        return $this->tableGateway->selectWith($select)->count();

    }


    public function markCompleted($studentId,$sessionId){
        $this->tableGateway->update([
            'completed'=>1
        ],[
            'student_id'=>$studentId,
            'session_id'=>$sessionId
        ]);
    }
}