<?php

namespace Intermatics;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Di\ServiceLocator;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\AdapterServiceFactory;
use Zend\Config\Reader\Xml;
use Zend\ServiceManager\ServiceLocatorInterface;  
use Zend\Db\Metadata\Metadata;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface; 
use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilter;

use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Select;

abstract class BaseTable implements InputFilterAwareInterface {
	protected  $tableName = '';
	public $tableGateway ;
	protected $serviceLocator;
	protected  $primary;
	protected $adapter;
	protected  $inputFilter;
	public $db;
	public $config;
	public $cache;
    protected $accountId = false;
	
	
	/**
	 * Constructor sets service locator and prepares tableGateway
	 * @param ServiceLocatorInterface $serviceLocator
	 */
	function __construct(ServiceLocatorInterface $serviceLocator =  null) {

        if(!isset($serviceLocator)){
            $serviceLocator = $GLOBALS['serviceManager'];
        }
	 	
		$this->setServiceLocator($serviceLocator);
 
		$dbAdapter =  $this->serviceLocator->get('Zend\Db\Adapter\Adapter');

		$this->adapter = $dbAdapter;
		$gateWay = new TableGateway($this->tableName, $dbAdapter); 
		$this->tableGateway = $gateWay;
		
		$this->db = new DbAdapterWrapper($gateWay,$serviceLocator,$this->primary);
		$this->config = new DbConfig();
		$this->cache = new DbCache();

        //add default primary
        if(empty($this->primary)){
            $this->primary= $this->tableName.'_id';
        }
		
	}
	
	 
	
 
	
	
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
		$this->serviceLocator= $serviceLocator;
	}
	 
	public function getServiceLocator()
	{
		return $this->serviceLocator;
	}
	
	
	public function detectPrimary()
	{
		if (null === $this->primary) {
			
			$metadata = new Metadata($this->adapter); 
			$constraints = $metadata->getTable($this->tableName)->getConstraints();
		 
			foreach ($constraints AS $constraint) {
				if ($constraint->isPrimaryKey()) {
					$primaryColumns = $constraint->getColumns();
					$this->primary = $primaryColumns[0];
				}
			}
			 
			
		}
	
		 
	}
	
	public function getPrimary()
	{
		if (!isset($this->primary)) {
			$this->detectPrimary();
		}
		
		return $this->primary;
	}
	
	public function setPrimary($primary)
	{
		$this->primary = $primary;
	}
	
	public function getRecord($id)
	{
		$primary = $this->getPrimary();
        if($this->accountId && !GLOBAL_ACCESS){
            $row= $this->tableGateway->select(array($primary=>$id,'account_id'=>ADMIN_ID))->current();
        }
        else{
            $row= $this->tableGateway->select(array($primary=>$id))->current();
        }


		if (!$row) {
			throw new \Exception("Could not find row $id");
		}
		return $row;
	}
	
	public function deleteRecord($id)
	{
		$primary = $this->getPrimary();
        if($this->accountId && !GLOBAL_ACCESS){
            $total = $this->tableGateway->delete(array($primary=>$id,'account_id'=>ADMIN_ID));
        }
        else{
            $total = $this->tableGateway->delete(array($primary=>$id));
        }

		return $total;
	}
 	
	public function getRecords()
	{
        if($this->accountId && !GLOBAL_ACCESS){
            return $this->tableGateway->select(['account_id'=>ADMIN_ID]);
        }
        else{
            return $this->tableGateway->select();
        }


	}
	
  public function setInputFilter(InputFilterInterface $inputFilter)
    {
    	throw new \Exception("Not used");
    }
    
    public function getInputFilter()
    {
    	if (!$this->inputFilter)
    	{
    		$inputFilter = new InputFilter();
    		$factory = new Factory();
    		
    		
    		
    		
    	}
    }
    
    public function addRecord($data)
    {
        if($this->accountId){
            $data['account_id']= ADMIN_ID;
        }
    	$data = $this->clearInvalid($data);
    	$this->tableGateway->insert($data);
    	$id = $this->tableGateway->getLastInsertValue();
    	return $id;
    }
    
    public function update($data,$id){
        if($this->accountId && !GLOBAL_ACCESS){
              $this->tableGateway->update($data,array($this->primary=>$id,'account_id'=>ADMIN_ID));
        }
        else{
            $this->tableGateway->update($data,array($this->primary=>$id));
        }


    }
    
    public function saveRecord($data)
    {
    		
    	$data = $this->clearInvalid($data);
    	$id = (int)$data[$this->primary];
    	if ($id == 0) {
     
    		//add record
    		$id = $this->addRecord($data);
    	}
    	else {
    		//update merchant
    		if ($this->getRecord($id)) {
    			$this->tableGateway->update($data, array($this->primary => $id));
    		} else {
    			throw new \Exception('Record id does not exist');
    		}
    	}
    
    	return $id;
    }
    
    /**
     * Clears the submit button from form fields
     * @param array $data
     * @return array
     */
    public function clearInvalid($data)
    {
    	unset($data['submit']);
    	return $data;
    }
    


    public function getPaginatedRecords($paginated=false,$id=null)
    {
        $select = new Select($this->tableName);
        $select->order($this->primary.' desc');

        if($this->accountId && !GLOBAL_ACCESS){
            $select->where(['account_id'=>ADMIN_ID]);
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
    public function getTotal()
    {
        if($this->accountId && !GLOBAL_ACCESS){
            $rowset = $this->tableGateway->select(['account_id'=>ADMIN_ID]);
        }
        else{
            $rowset = $this->tableGateway->select();
        }

    	$total = $rowset->count();
    	return $total;
    }

	public function getLanguage($language_id=null)
	{
		return 1;
	}
	
	public function setRegistry($registry)
	{
		$this->registry = $registry;
		foreach ($this->registry->getData() as $key=>$value)
		{
	
			$this->$key = $value;
		}
	}

    public function recordExists($id){
        $total = $this->tableGateway->select([$this->primary=>$id])->count();
        if(empty($total)){
            return false;
        }
        else{
            return true;
        }
    }

    public function getLimitedRecords($limit)
    {
        $select = new Select($this->tableName);

        $select->limit($limit);
        if($this->accountId && !GLOBAL_ACCESS){
            $select->where(['account_id'=>ADMIN_ID]);
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getGateway(){
        return $this->tableGateway;
    }


}

?>