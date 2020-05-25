<?php

namespace Intermatics;

use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\ServiceLocatorInterface;
use Merchant\Model\MerchantsTable;

class MerchantTableGateway extends TableGateway {
	
	protected $serviceLocator;
	protected $primary;
	
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
		$this->serviceLocator = $serviceLocator;
	}
	/*
	public function insert($set)
	{
		$authService = $this->getServiceLocator()->get('MerchantAuthService');
		$array = $authService->getStorage()->read(); 
		$email = $array['email'];
		
		$merchantsTable = new MerchantsTable($this->serviceLocator);
		$row = $merchantsTable->getMerchantWithEmail($email);
		
		$set['merchant_id'] = $row->merchant_id;
		parent::insert($set);
	}
	*/
	public function setPrimary($primary)
	{
		$this->primary = $primary;
	}
	
	 
	
	public function hasPermission($id)
	{
	//	$row = 
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
		$row = $this->getMerchant();
		return $row->merchant_id;
	}
	
	public function getServiceLocator()
	{
		return $this->serviceLocator;
	}
}

?>