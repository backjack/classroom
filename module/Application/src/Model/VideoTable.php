<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 9/15/2018
 * Time: 10:55 PM
 */

namespace Application\Model;


use Application\Entity\Video;
use Intermatics\BaseTable;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class VideoTable extends BaseTable
{

    protected $tableName = 'video';
    protected $primary = 'video_id';

   

    public function getVideos($paginated=false,$filter=null,$order=null)
    {
       
            $select = new Select($this->tableName);
      

        if(!GLOBAL_ACCESS){
            $select->where(['account_id'=>ADMIN_ID]);
        }

        switch($order){
            case 'asc':
                $select->order('name asc');
                break;
            case 'desc':
                $select->order('name desc');
                break;
            case 'recent':
                $select->order('video_id desc');
                break;
            default:
                $select->order('video_id desc');
                break;

        }


        if(isset($filter))
        {
            $filter = $this->db->escape($filter);

            $select->where("MATCH (name,description) AGAINST ('$filter' IN NATURAL LANGUAGE MODE)");

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


}