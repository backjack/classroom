<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 8/7/2016
 * Time: 3:33 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class SessionTable extends BaseTable {

    protected $tableName = 'session';
    protected $primary = 'session_id';
    protected $accountId = true;

    public function getLimitedRecords($limit)
    {
        $select = new Select($this->tableName);
        $select->order('session_name')
                ->limit($limit);

        if(!GLOBAL_ACCESS){
            $select->where(['account_id'=>ADMIN_ID]);
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }


    public function getPaginatedRecords($paginated=false,$id=null,$activeOnly=false,$filter=null,$group=null,$order=null,$type=null,$futureOnly=false,$payment=null)
    {


        if(empty($group)){
            $select = new Select($this->tableName);
        }
        else{
            $select = new Select('session_to_session_category');
            $select->join('session','session_to_session_category.session_id=session.session_id',['session_name','description','venue','session_date','session_status','session_end_date','session_type']);
            $select->join('session_category','session_to_session_category.session_category_id=session_category.session_category_id',['category_name']);
            $select->where(['session_category.session_category_id'=>$group]);
        }

        if(!GLOBAL_ACCESS){
            $select->where([$this->tableName.'.account_id'=>ADMIN_ID]);
        }

        if(!empty($type)){
            if(is_array($type)){
                $sql= '(';
                $count = 0;
                foreach($type as $value){
                   if(!empty($count)){
                       $sql.= ' OR ';
                   }
                    $sql .= 'session_type=\''.$value.'\'';
                    $count++;
                }
                $sql .= ')';

                $select->where($sql);
            }else{
                $select->where(['session_type'=>$type]);
            }

        }




        switch($order){
            case 'asc':
                $select->order('session_name asc');
                break;
            case 'desc':
                $select->order('session_name desc');
                break;
            case 'recent':
                $select->order('session_id desc');
                break;
            case 'date':
                $select->order('session_date desc');
                break;
            case 'priceAsc':
                $select->order('amount asc');
                break;
            case 'priceDesc':
                $select->order('amount desc');
                break;
            default:
                $select->order('session_id desc');
                break;

        }

        if(isset($filter))
        {
            $filter = $this->db->escape($filter);
            $select->where("MATCH (session_name,session.description,venue) AGAINST ('$filter' IN NATURAL LANGUAGE MODE)");
        }

        if($activeOnly){
            $select->where(array('session_status'=>1));
        }

        if(is_numeric($payment)){
            $select->where(array('payment_required'=>$payment));
        }

        if($futureOnly){
          //  $select->where(array('session_end_date > '.time()));
            $time = time();
        //    $select->where("(session_end_date > {$time} OR session_end_date=0 )");
            $select->where("( (session_end_date IS NULL OR session_end_date=0 OR session_end_date > '$time') AND (enrollment_closes IS NULL OR enrollment_closes=0 OR enrollment_closes > '$time')  )");

        }

        if($paginated)
        {
            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getTotalRecords($paginated=false,$id=null,$activeOnly=false,$filter=null,$group=null,$order=null,$type=null,$futureOnly=false,$payment=null)
    {


        if(empty($group)){
            $select = new Select($this->tableName);
        }
        else{
            $select = new Select('session_to_session_category');
            $select->join('session','session_to_session_category.session_id=session.session_id',['session_name','description','venue','session_date','session_status','session_end_date','session_type']);
            $select->join('session_category','session_to_session_category.session_category_id=session_category.session_category_id',['category_name']);
            $select->where(['session_category.session_category_id'=>$group]);
        }

        if(!GLOBAL_ACCESS){
            $select->where([$this->tableName.'.account_id'=>ADMIN_ID]);
        }

        if(!empty($type)){
            if(is_array($type)){
                $sql= '(';
                $count = 0;
                foreach($type as $value){
                    if(!empty($count)){
                        $sql.= ' OR ';
                    }
                    $sql .= 'session_type=\''.$value.'\'';
                    $count++;
                }
                $sql .= ')';

                $select->where($sql);
            }else{
                $select->where(['session_type'=>$type]);
            }

        }




        switch($order){
            case 'asc':
                $select->order('session_name asc');
                break;
            case 'desc':
                $select->order('session_name desc');
                break;
            case 'recent':
                $select->order('session_id desc');
                break;
            case 'date':
                $select->order('session_date desc');
                break;
            case 'priceAsc':
                $select->order('amount asc');
                break;
            case 'priceDesc':
                $select->order('amount desc');
                break;
            default:
                $select->order('session_id desc');
                break;

        }

        if(isset($filter))
        {
            $filter = $this->db->escape($filter);
            $select->where("MATCH (session_name,session.description,venue) AGAINST ('$filter' IN NATURAL LANGUAGE MODE)");
        }

        if($activeOnly){
            $select->where(array('session_status'=>1));
        }

        if(is_numeric($payment)){
            $select->where(array('payment_required'=>$payment));
        }

        if($futureOnly){
        //    $select->where(array('session_end_date > '.time()));
            $time = time();
//            $select->where("(session_end_date > {$time} OR session_end_date=0 )");
            $select->where("( (session_end_date IS NULL OR session_end_date=0 OR session_end_date > '$time') AND (enrollment_closes IS NULL OR enrollment_closes=0 OR enrollment_closes > '$time')  )");

        }


        $rowset = $this->tableGateway->selectWith($select);
            return $rowset->count();

    }




    public function getPaginatedCourseRecords($paginated=false,$id=null,$activeOnly=false,$filter=null,$group=null,$order=null,$type=null)
    {


        if(empty($group)){
            $select = new Select($this->tableName);
        }
        else{

            $select = new Select('session_to_session_category');
            $select->join('session','session_to_session_category.session_id=session.session_id',['session_name','description','venue','session_date','session_status','session_end_date','session_type','short_description','picture','enrollment_closes','amount','payment_required']);
            $select->join('session_category','session_to_session_category.session_category_id=session_category.session_category_id',['category_name']);
            $select->where(['session_category.session_category_id'=>$group]);
        }

        if(!GLOBAL_ACCESS){
            $select->where([$this->tableName.'.account_id'=>ADMIN_ID]);
        }

        if(!empty($type)){
            if(is_array($type)){
                $sql= '(';
                $count = 0;
                foreach($type as $value){
                    if(!empty($count)){
                        $sql.= ' OR ';
                    }
                    $sql .= 'session_type=\''.$value.'\'';
                    $count++;
                }
                $sql .= ')';

                $select->where($sql);
            }else{
                $select->where(['session_type'=>$type]);
            }

        }




        switch($order){
            case 'asc':
                $select->order('session_name asc');
                break;
            case 'desc':
                $select->order('session_name desc');
                break;
            case 'recent':
                $select->order('session_id desc');
                break;
            case 'date':
                $select->order('session_date desc');
                break;
            case 'priceAsc':
                $select->order('amount asc');
                break;
            case 'priceDesc':
                $select->order('amount desc');
                break;
            default:
                $select->order('session_id desc');
                break;

        }

        if(isset($filter))
        {
            $filter = $this->db->escape($filter);
            $select->where("MATCH (session_name,description,venue) AGAINST ('$filter' IN NATURAL LANGUAGE MODE)");
        }

        if($activeOnly){
            $select->where(array('session_status'=>1));
        }

       $time = time();
        $select->where("( (session_end_date IS NULL OR session_end_date =0 OR session_end_date > '$time') AND (enrollment_closes IS NULL OR enrollment_closes =0 OR enrollment_closes > '$time')  )");


        if($paginated)
        {
            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getTotalCourseRecords($paginated=false,$id=null,$activeOnly=false,$filter=null,$group=null,$order=null,$type=null)
    {


        if(empty($group)){
            $select = new Select($this->tableName);
        }
        else{
            $select = new Select('session_to_session_category');
            $select->join('session','session_to_session_category.session_id=session.session_id',['session_name','description','venue','session_date','session_status','session_end_date','session_type','short_description','picture','enrollment_closes']);
            $select->join('session_category','session_to_session_category.session_category_id=session_category.session_category_id',['category_name']);
            $select->where(['session_category.session_category_id'=>$group]);
        }

        if(!GLOBAL_ACCESS){
            $select->where([$this->tableName.'.account_id'=>ADMIN_ID]);
        }

        if(!empty($type)){
            if(is_array($type)){
                $sql= '(';
                $count = 0;
                foreach($type as $value){
                    if(!empty($count)){
                        $sql.= ' OR ';
                    }
                    $sql .= 'session_type=\''.$value.'\'';
                    $count++;
                }
                $sql .= ')';

                $select->where($sql);
            }else{
                $select->where(['session_type'=>$type]);
            }

        }




        switch($order){
            case 'asc':
                $select->order('session_name asc');
                break;
            case 'desc':
                $select->order('session_name desc');
                break;
            case 'recent':
                $select->order('session_id desc');
                break;
            case 'date':
                $select->order('session_date desc');
                break;
            case 'priceAsc':
                $select->order('amount asc');
                break;
            case 'priceDesc':
                $select->order('amount desc');
                break;
            default:
                $select->order('session_id desc');
                break;

        }

        if(isset($filter))
        {
            $filter = $this->db->escape($filter);
            $select->where("MATCH (session_name,description,venue) AGAINST ('$filter' IN NATURAL LANGUAGE MODE)");
        }

        if($activeOnly){
            $select->where(array('session_status'=>1));
        }

        $time = time();
        $select->where("( (session_end_date IS NULL OR session_end_date =0 OR session_end_date > '$time') AND (enrollment_closes IS NULL OR enrollment_closes =0 OR enrollment_closes > '$time')  )");


        $rowset = $this->tableGateway->selectWith($select);
        return $rowset->count();
    }


    public function getValidSessions($paginated=false,$type=null)
    {
        $select = new Select($this->tableName);
        $select->order('session_date desc');


            $select->where(array('session_status'=>1,'session_end_date > '.time()));
        if(!GLOBAL_ACCESS){
            $select->where([$this->tableName.'.account_id'=>ADMIN_ID]);
        }
        if(!empty($type)){
            if(is_array($type)){
                $sql= '(';
                $count = 0;
                foreach($type as $value){
                    if(!empty($count)){
                        $sql.= ' OR ';
                    }
                    $sql .= 'session_type=\''.$value.'\'';
                    $count++;
                }
                $sql .= ')';

                $select->where($sql);
            }else{
                $select->where(['session_type'=>$type]);
            }

        }
        if($paginated)
        {
            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function checkSession($id){
        $rowset = $this->tableGateway->select(array('session_id'=>$id));
        $total = $rowset->count();
        if(empty($total)){
            $data = array(
                'session_id'=>$id,
                'session_name'=>'Random Session '.time(),
                'session_date'=> mktime(null,null,null,null,null,2006),
                'session_status'=>0
            );
            $this->addRecord($data);
        }


    }


    public function getUpcomingCourses($days=3){
        $upperLimit = strtotime('tomorrow midnight') - 1;

        $timestamp = strtotime("+$days day");

        $timeLimit = mktime(23,59,0,date('n',$timestamp),date('j',$timestamp),date('Y',$timestamp));


        $select = new Select($this->tableName);
        $select->where(['session_end_date < '.$timeLimit,'session_end_date > '.$upperLimit,'session_type'=>'c'])
            ->order('session_end_date');
        $rowset = $this->tableGateway->selectWith($select);
        $rowset->buffer();
        return $rowset;

    }

    public function getClosingCourses($days=3){
        $upperLimit = strtotime('tomorrow midnight') - 1;

        $timestamp = strtotime("+$days day");

        $timeLimit = mktime(23,59,0,date('n',$timestamp),date('j',$timestamp),date('Y',$timestamp));


        $select = new Select($this->tableName);
        $select->where(['session_end_date > 0','session_end_date < '.$timeLimit,'session_end_date > '.$upperLimit,'session_date < '.time()])
            ->order('session_end_date');
        $rowset = $this->tableGateway->selectWith($select);
        $rowset->buffer();
        return $rowset;

    }

}