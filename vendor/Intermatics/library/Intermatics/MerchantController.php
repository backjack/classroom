<?php

namespace Intermatics;

use Intermatics\Opencart\Library\Image;
use Merchant\Model\CategoryTable;
use Merchant\Model\SettingTable;
use Intermatics\Opencart\Library\Config; 
use Zend\View\Model\ViewModel;
class MerchantController extends BaseController {
	 
 	 
	protected $languageDir = 'admin';
	
	
	public function __construct(){
		parent::__construct(); 
 
		
		//check setting for settings
	/*
		$config = new Config(); 
		 
		foreach ($this->session->settings->rows as $setting)
		{
			 
			if (!$setting['serialized']) {
				$config->set($setting['key'], $setting['value']);
			} else {
				$config->set($setting['key'], unserialize($setting['value']));
			}
		}
		
		$config->set('config_language_id',$this->session->langaugeId);
		
		$this->config=$config;
		*/
		//set registry in session
		$this->registry = 	$GLOBALS['registry'];
			
		
		unset($this->config,$this->load,$this->session);
	}
	
	public function __get($key) {
	
			
		if ($this->registry->has($key)) {
			return $this->registry->get($key);
		}
	
	}
	
	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}
	
	
	public function getMerchant()
	{
		$categoryTable = new CategoryTable($this->getServiceLocator());
		$row = $categoryTable->getMerchant();
		return $row;
	
	
	}
	
	public function getMerchantId()
	{
		$row = $this->getMerchant();
		return $row->merchant_id;
	}
	
	public function getViewModel($data=null)
	{
	
		//set page info
		$title = $this->document->getTitle();
		if (!empty($title)) {
			$this->data['pageTitle'] = $this->document->getTitle();
		}
		
		$description=$this->document->getDescription();
		if (!empty($description)) {
			$this->data['layout_description'] = $this->document->getDescription();
		}
		$keywords = $this->document->getKeywords();
		if (!empty($keywords)) {
			$this->data['layout_keywords'] = $this->document->getKeywords();
		}
	
	
		$this->data['$breadcrumbs']= array();
		$this->data['header'] = '';
		$this->data['footer'] = '';
		$this->data['language'] = $this->language;
	 		
		$viewModel = new ViewModel($this->data);
		
		if (!empty($this->template)) {
			
			$this->template = str_replace('.tpl', '', $this->template);
			$viewModel->setTemplate('opencart/'.$this->template);
			
		}
		
		
		return $viewModel;
	}
}

?>