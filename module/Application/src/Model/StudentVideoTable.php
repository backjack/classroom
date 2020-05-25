<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 2/1/2017
 * Time: 1:37 PM
 */

namespace Application\Model;


use Application\Entity\StudentVideo;
use Intermatics\BaseTable;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class StudentVideoTable extends BaseTable
{

    protected $tableName = 'student_video';
    protected $primary = 'student_video_id';

    public function addVideoForStudent($studentId,$videoId){

        $record= StudentVideo::where('student_id',$studentId)->where('video_id',$videoId)->first();
        if(!$record){
            $record = StudentVideo::create([
               'video_id'=>$videoId,
                'student_id'=>$studentId
            ]);
        }

        return $record;

    }


    public function hasVideo($studentId,$videoId){
        $record= StudentVideo::where('student_id',$studentId)->where('video_id',$videoId)->count();
        return !empty($record);
    }


}