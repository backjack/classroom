<?php
namespace Intermatics\Opencart\Engine;
final class MerchantLoader {
	protected $registry;
	protected  $serviceLocator;

	public function __construct($registry,$serviceLocator) {
		$this->registry = $registry;
		$this->serviceLocator = $serviceLocator;
	}

	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

	public function library($library) {
		$file = DIR_SYSTEM . 'Library/' . ucwords($library) . '.php';
		$class = '\Intermatics\Opencart\Library\\'.ucwords(preg_replace('#/#', '\\', $library));
		$class = new $class($this->serviceLocator);
		if (file_exists($file)) {
			
			if (method_exists($class, 'setRegistry')) {
				$class->setRegistry($this->registry);
			}
			
			 
			
			include_once($file);
		} else {
			trigger_error('Error: Could not load library ' . $library . '!');
			exit();					
		}
	}

	public function helper($helper) {
		$file = DIR_SYSTEM . 'helper/' . $helper . '.php';

		if (file_exists($file)) {
			include_once($file);
		} else {
			trigger_error('Error: Could not load helper ' . $helper . '!');
			exit();					
		}
	}

	public function model($model) {
	 
		$mname = $model;
		$words = explode('/', $model);
		foreach ($words as $key=>$value)
		{
			if (preg_match('#_#', $value)) {
				$sub = explode('_', $value);
				
				foreach ($sub as $key=>$value)
				{
					$sub[$key] = ucfirst($value);
				}
				
				$value = implode('', $sub);
			}
			
			$words[$key] = ucfirst($value);
			 
		}
		$model = implode('/', $words);
		 
 
		$file  = DIR_APPLICATION . 'Model/' . ucwords($model) . '.php';
		
		 
		
		//$class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);
		$class = '\Merchant\Model\\'.ucwords(preg_replace('#/#', '\\', $model));
		//get model path
		
		$class = new $class($this->serviceLocator);
		 
		
		if (file_exists($file)) { 
			
			 
			//include_once($file);
			
			if (method_exists($class, 'setRegistry')) {
				$class->setRegistry($this->registry);
			}
			 
			$this->registry->set('model_' . str_replace('/', '_', strtolower($mname)), $class);
			
		 
			
		
		} else {
			trigger_error('Error: Could not load model ' . $model . '!');
			exit();					
		}
	}

	public function database($driver, $hostname, $username, $password, $database) {
		/*
		$file  = DIR_SYSTEM . 'database/' . $driver . '.php';
		$class = 'Database' . preg_replace('/[^a-zA-Z0-9]/', '', $driver);

		if (file_exists($file)) {
			include_once($file);

			$this->registry->set(str_replace('/', '_', $driver), new $class($hostname, $username, $password, $database));
		} else {
			trigger_error('Error: Could not load database ' . $driver . '!');
			exit();				
		}
		*/
	}

	public function config($config) {
		$this->config->load($config);
	}

	public function language($language) {
		return $this->language->load($language);
	}		
} 
?>