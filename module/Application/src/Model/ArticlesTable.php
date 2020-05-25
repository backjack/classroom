<?php

namespace Application\Model;

use Intermatics\BaseTable;
use Zend\Db\Sql\Select;

class ArticlesTable extends BaseTable {
	
	protected $tableName = 'articles';
	protected $primary = 'article_id';
	
	private $path = '';
	public function getRecordWithAlias($alias)
	{
		$row = $this->tableGateway->select(array('alias'=>$alias))->current();
		return $row;
	}
	
	public function aliasExists($alias)
	{
		$rowset= $this->tableGateway->select(array('alias'=>$alias));
		$total = $rowset->count();
		if (empty($total)) {
			return false;
		}
		else{
			return true;
		}
	}

    public function getValidAlias($alias){
        $alias = substr(trim($alias),0,100);

        $original = $alias;
        $count = 0;
        do{
            $alias= $original;
            if(!empty($count)){
                $alias = $alias.'-'.$count;
            }
            $count++;
        }while($this->aliasExists($alias));


        return $alias;
    }

    public function getArticlesForParent($parentId,$topNav=null,$bottomNav=null,$visibility=null){
        $select = new Select($this->tableName);
        $select->where(['parent'=>$parentId])
                ->order('sort_order')
                ->limit(500);
        if($topNav){
            $select->where(['top_nav'=>$topNav]);
        }

        if($bottomNav){
            $select->where(['bottom_nav'=>$bottomNav]);
        }

        if($visibility){
            $select->where("(visibility='{$visibility}' OR visibility='b')");
        }

        $rowset = $this->tableGateway->selectWith($select);
        return $rowset;
    }

    public function hasChildren($parentId,$topNav=null,$bottomNav=null){

        $select = new Select($this->tableName);
        $select->where(['parent'=>$parentId])
            ->order('sort_order')
            ->limit(500);
        if($topNav){
            $select->where(['top_nav'=>$topNav]);
        }

        if($bottomNav){
            $select->where(['bottom_nav'=>$bottomNav]);
        }

        $rowset = $this->tableGateway->selectWith($select);


        $total = $rowset->count();
        if(!empty($total)){
            return true;
        }
        else{
            return false;
        }
    }

    public function articleName($id){
        $this->path = '';
        $this->getPath($id);
        $count = strlen($this->path);
        $this->path= substr($this->path,0,$count-3);
        return $this->path;
    }

    public function getPath($id){
        $row = $this->getRecord($id);

        $this->path = $row->article_name.' > '.$this->path;
        if(!empty($row->parent)){
            $this->getPath($row->parent);
        }

    }


}

?>