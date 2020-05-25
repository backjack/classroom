<?php

namespace Application\Model;

use Intermatics\BaseTable;
use Zend\Db\Sql\Select;
class NewsflashTable extends BaseTable {
	
	protected $tableName = 'newsflash';
	protected $primary = 'newsflash_id';
    protected $accountId = true;
	
	public function getNews($limit=5)
	{
		$select = new Select($this->tableName);
		$select->limit($limit);
		$select->order($this->primary.' desc');
        if(!GLOBAL_ACCESS){
            $select->where([$this->tableName.'.account_id'=>ADMIN_ID]);
        }
		$rowset = $this->tableGateway->selectWith($select);
		return $rowset;
	}
	
}

?>