<?php

namespace Intermatics;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\ServiceLocatorInterface;
use Merchant\Model\MerchantsTable;

class DbAdapterWrapper {
	
	protected $tableGateway;
	protected $serviceLocator;
	protected $dbh;
	protected $affected;
	protected $primary;

	function __construct(TableGateway $tableGateway,ServiceLocatorInterface $serviceLocator,$primary) {
		
		 
		$this->tableGateway = $tableGateway;
		$this->serviceLocator =$serviceLocator;
		
		$config = $this->serviceLocator->get('Config');
		$db = $config['db'];
		
		$this->dbh = new \PDO($db['dsn'], $db['username'], $db['password']);

		$this->primary = $primary;
	}
	
	
	public function query($sql)
	{
		$adapter = $this->tableGateway->getAdapter();
		$statement = $adapter->createStatement($sql);
		$result = $statement->execute();
	//	$adapter->getDriver()->getLastGeneratedValue();
		$this->affected = $result->getAffectedRows();
		$i = 0;
		
		$data = array();
		
		try{
		
			foreach ($result as $row) {
				
				$data[$i] = $row;
			
				$i++;
				
				
			}
		}
		catch(\Exception $ex)
		{
			
		}
		
		$query = new \stdClass();
		$query->row = isset($data[0]) ? $data[0] : array();
		$query->rows = $data;
		$query->num_rows = $i;
		
		unset($data);
		$this->setUserId();
	 
		 
		return $query;
		
		 
	}
	
	public function escape($param)
	{
		
		$param = $this->dbh->quote($param);
		
		$param = substr($param, 1);
		
		$len = strlen($param);
		$param = substr($param, 0,$len-1);
		 
		
		return $param;
	}
	
	
	public function countAffected() {
		return $this->affected;
	}
	
	public function getLastId() {
		$id = $this->tableGateway->getAdapter()->getDriver()->getLastGeneratedValue();
	 
		return $id;
	}
	
	public function setUserId()
	{
		$id = $this->tableGateway->getLastInsertValue();
		if(!empty($id))
		{
			$role = UtilityFunctions::getRole();
			try{
				
						switch ($role)
						{
							case 'merchant':
								//update
								$mid = $this->getMerchantId();
								$this->tableGateway->update(array('merchant_id'=>$mid),array($this->primary=>$id));
					
								break;
							case 'customer':
								
								break; 
								 
						}
			
			
			}
			catch (\Exception $ex)
			{
			
			}
				
			
		}
	}
	
	public function getMerchant()
	{
		$authService = $this->serviceLocator->get('MerchantAuthService');
		$array = $authService->getStorage->read();
		$email = $array['email'];
	
		$merchantsTable = new MerchantsTable($this->serviceLocator);
		$row = $merchantsTable->getMerchantWithEmail($email);
	}
	
	public function getMerchantId()
	{
		$row = $this->getMerchant();
		return $row->merchant_id;
	}
	
	
}

?>