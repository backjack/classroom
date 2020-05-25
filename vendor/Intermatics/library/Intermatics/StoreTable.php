<?php

namespace Intermatics;

use Intermatics\Opencart\Library\Language;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Application\Model\MerchantsTable;
use Zend\Session\Container;

class StoreTable extends BaseTable {
	
	public $language;
	protected $languageDir = 'catalog';
	protected $registry;
	
	function __construct(ServiceLocatorInterface $serviceLocator) {
		
		parent::__construct($serviceLocator);
		$this->setServiceLocator($serviceLocator);
		
		$this->language = new Language($this->languageDir.'/english');
		$this->language->load('english');
		
		//set registry from session 
		$session = new  Container('controller'); 
		//set registry in session
		$this->registry = 	$GLOBALS['registry'];
			
			
		unset($this->config);
	}
	

	
	public function getMerchant()
	{ 
		$merchantsTable = new MerchantsTable($this->getServiceLocator());
		$row = $merchantsTable->getMerchant($this->getMerchantId());
		return $row;
	 
	}
	

	public function __get($key) {
		if ($this->registry->has($key)) {
			return $this->registry->get($key);
		}
	
	}
	
	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}
	
	public function getMerchantId()
	{
 		return MID;
	}
	
	
 
	
	public function getMerchantRecords($paginated=false)
	{
		$select = new Select($this->tableName);
		$select->where(array('merchant_id'=>$this->getMerchantId()));
	
	
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

?>