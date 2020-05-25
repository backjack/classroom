<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/5/2017
 * Time: 11:33 AM
 */

namespace Application\Model;


use Intermatics\BaseTable;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class BookmarkTable extends BaseTable {

    protected $tableName = 'bookmark';
    protected $primary = 'bookmark_id';

    public function addBookMark($studentId,$lecturePageId,$sessionId){
        $total = $this->tableGateway->select(['student_id'=>$studentId,'lecture_page_id'=>$lecturePageId,'session_id'=>$sessionId])->count();
        if(empty($total)){
            $data = [
                'student_id'=>$studentId,
                'lecture_page_id'=>$lecturePageId,
                'session_id'=>$sessionId,
                'created_on'=>time()
            ];
            return $this->addRecord($data);
        }
        else{
            return false;
        }

    }

    public function getPaginatedStudentRecords($paginated=false,$studentId)
    {
        $select = new Select($this->tableName);
        $select->order($this->primary.' desc');
        $select->where(['student_id'=>$studentId]);
        $select->join('lecture_page','bookmark.lecture_page_id=lecture_page.lecture_page_id',['page_title'=>'title','page_content'=>'content'])
            ->join('lecture','lecture_page.lecture_id=lecture.lecture_id',['lecture_title','lecture_id'])
                ->join('lesson','lecture.lesson_id=lesson.lesson_id',['lesson_name','content','lesson_id'])
                ->join('session',$this->tableName.'.session_id=session.session_id',['session_name']);


        if($paginated)
        {
            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }




}