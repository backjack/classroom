<?php
namespace Intermatics\Opencart\Library;
use Intermatics\DbAdapterWrapper;
use Zend\Db\TableGateway\TableGateway;
class DB {
	private $driver;

	public function __construct($sm) {
		/*
		$file = DIR_DATABASE . $driver . '.php';

		if (file_exists($file)) {
			require_once($file);

			$class = 'DB' . $driver;

			$this->driver = new $class($hostname, $username, $password, $database);
		} else {
			exit('Error: Could not load database driver type ' . $driver . '!');
		}
		*/	
		$dbAdapter =  $sm->get('Zend\Db\Adapter\Adapter'); 
		$gateWay = new TableGateway('setting', $dbAdapter); 
		$this->driver = new DbAdapterWrapper($gateWay, $sm, '1');
	}

	public function query($sql) {
		return $this->driver->query($sql);
	}

	public function escape($value) {
		return $this->driver->escape($value);
	}

	public function countAffected() {
		return $this->driver->countAffected();
	}

	public function getLastId() {
		return $this->driver->getLastId();
	}
}
?>