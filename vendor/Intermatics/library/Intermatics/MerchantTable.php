<?php

namespace Intermatics;
use Merchant\Model\MerchantsTable;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Intermatics\Opencart\Library\Language;
class MerchantTable extends BaseTable {
	
	public $language;
	protected $languageDir = 'admin';
	/**
	 * Constructor sets service locator and prepares tableGateway
	 * @param ServiceLocatorInterface $serviceLocator
	 */
	function __construct(ServiceLocatorInterface $serviceLocator) {
		 
		parent::__construct($serviceLocator);
		$this->setServiceLocator($serviceLocator);
	
		$dbAdapter =  $this->serviceLocator->get('Zend\Db\Adapter\Adapter');
	
		$this->adapter = $dbAdapter;
		$gateWay = new MerchantTableGateway($this->tableName, $dbAdapter);
		$gateWay->setServiceLocator($this->getServiceLocator());
		$gateWay->setPrimary($this->primary);
		$this->tableGateway = $gateWay;
		 
		$this->language = new Language($this->languageDir.'/english');
		$this->language->load('english');
	}
	
	public function getMerchant()
	{
		$authService = $this->getServiceLocator()->get('MerchantAuthService');
		 
		$array = $authService->getStorage()->read();
		$email = $array['email'];
		 
		$merchantsTable = new MerchantsTable($this->serviceLocator);
		$row = $merchantsTable->getMerchantWithEmail($email);
		return $row;
		
		
	}
	
	public function getMerchantId()
	{
		$authService = $this->getServiceLocator()->get('MerchantAuthService');
		 
		$array = $authService->getStorage()->read();
		$email = $array['email'];
		 
		$merchantsTable = new MerchantsTable($this->serviceLocator);
		$row = $merchantsTable->getMerchantWithEmail($email);
		return $row->merchant_id;
	}
	
	
	 public function verifyPermission($id)
	 {
	 	$row = $this->getRecord($id);
	 	if ($this->getMerchantId()!= $row->merchant_id) {
	 		throw new \Exception('You do not have permission to do this');
	 	}
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