<?php

namespace Intermatics;

use Intermatics\BaseController;
use Intermatics\Opencart\Library\Config; 
use Zend\View\Model\ViewModel;
use Intermatics\Opencart\Engine\Registry;
use Store\Model\SettingTable;

class StoreController extends BaseController {
	
	protected $languageDir = 'catalog';
	protected $registry;
	
	public function __construct() {
		parent::__construct ();
		
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
		$config->set('store_id',0);
		$config->set('config_url', $this->getBaseUrl().'/');
		$config->set('config_ssl', $this->getBaseUrl().'/');
		
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
	
	public function initialize()
	{
		$settingTable = new SettingTable($this->getServiceLocator());
		$this->session->data['language'] = $settingTable->getLanguage();
		 
		//load dependencies
		$this->load->model('catalog/information');
		$this->load->model('design/layout');
		
		$this->setHeader();
		$this->setFooter();
		 
		$this->setColumnLeft();
		$this->data['layout_doc']= $this->document;
		$this->data['config'] = $this->config;
		$this->data['currency'] = $this->currency;
		$this->data['header'] = '';
		$this->data['footer'] = ''; 
		
	
	}
	
	public function getViewModel($data=null,$modules=true)
	{
		 
		
		//set page info
		if (!empty($this->document->getTitle())) {
			$this->data['layout_pageTitle'] = $this->document->getTitle();
		}
		
		if (!empty($this->document->getDescription())) {
			$this->data['layout_description'] = $this->document->getDescription();
		}
		
		if (!empty($this->document->getKeywords())) {
			$this->data['layout_keywords'] = $this->document->getKeywords();
		}
		
		
		if(!isset($data))
		{
			$data = $this->data;
		}
		
	
		
		
		$action = $this->getEvent()->getRouteMatch()->getParam('action', 'index');
		$controllerName = $this->getEvent()->getRouteMatch()->getParam('controller', 'index');
		
		$cname ='\\'.ucfirst($this->getEvent()->getRouteMatch()->getParam('__CONTROLLER__', 'index'));
		//$dcname = str_replace($cname, '', $controllerName);
		 
		 
		//get position of last dash
		$pos = strrpos($controllerName, '\\');
		$directory= substr($controllerName,0, $pos);
		
		$pos = strrpos($directory, '\\');
		$directory= strtolower(substr($directory,$pos+1));
 
		$controllerName = strtolower(substr($controllerName,strrpos($controllerName, '\\')+1));
		
			
		
		$e  = $this->getEvent();
		$controller      = $e->getTarget();
		$controllerClass = get_class($controller);
		$module = substr($controllerClass, 0, strpos($controllerClass, '\\'));
		  
		$viewModel = new ViewModel($data);
		$fileName = "module/$module/view/templates/".TID."/$directory/$controllerName/$action.phtml";
		 
		$defaultTemplate = "module/$module/view/store/$directory/$controllerName/$action.phtml";
		  
		//check if template file exists
		 if (file_exists($fileName)) {
		 	 $viewModel->setTemplate('templates/'.TID.'/'.$directory.'/'.$controllerName.'/'.$action);
		 }
		 elseif (file_exists($defaultTemplate))
		 {
		 	$viewModel->setTemplate('store/'.$directory.'/'.$controllerName.'/'.$action); 
		 }
		 elseif (!empty($this->template))
		 {
		 	$this->template = str_replace('.tpl', '.phtml', $this->template);
		 	$this->template = str_replace('default/', '', $this->template);
		 	
		 	 $folder ="module/$module/view";
		 	
		 	if (file_exists($folder.'/responsive/'.$this->template)) {
		 		$viewModel->setTemplate('responsive/'.str_replace('.phtml', '',$this->template));
		 	}
		 	else {
		 		$viewModel->setTemplate('default/'.str_replace('.phtml', '',$this->template));
		 	}
		 	
		 	
		 }
		 else {
		 //	$viewModel->setTemplate('templates/'.DTID.'/'.$controllerName.'/'.$action);
		 }  
		 
		 
		  if ($modules) {
		  	$viewModel->addChild($this->setContentBottom(),'content_bottom')
		  	->addChild($this->setColumnLeft(),'column_left')
		  	->addChild($this->setColumnRight(),'column_right')
		  	->addChild($this->setContentTop(),'content_top') ;
		  }
		 
		 
		return $viewModel;
	}
	
	public function getRoute()
	{
		$action = $this->getEvent()->getRouteMatch()->getParam('action', 'index');
		$controllerName = $this->getEvent()->getRouteMatch()->getParam('controller', 'index');
		
		$cname ='\\'.ucfirst($this->getEvent()->getRouteMatch()->getParam('__CONTROLLER__', 'index'));
		//$dcname = str_replace($cname, '', $controllerName);
			
			
		//get position of last dash
		$pos = strrpos($controllerName, '\\');
		$directory= substr($controllerName,0, $pos);
		
		$pos = strrpos($directory, '\\');
		$directory= strtolower(substr($directory,$pos+1));
		
		$controllerName = strtolower(substr($controllerName,strrpos($controllerName, '\\')+1));
		$route = $directory.'/'.$controllerName;
		return $route;
	}
	
	public function setHeader()
	{
		$this->data['layout_title'] = $this->document->getTitle();
		
		if (isset($this->oc_request->server['HTTPS']) && (($this->oc_request->server['HTTPS'] == 'on') || ($this->oc_request->server['HTTPS'] == '1'))) {
			$server = $this->getBaseUrl();
		} else {
			$server = $this->getBaseUrl();
		}
		$server .= '/';
		
		$this->data['layout_base'] = $server;
		$this->data['layout_sitemap'] = $this->oc_url('store/information/sitemap');
		$this->data['layout_text_sitemap'] = $this->language->get('text_sitemap');
		$this->data['layout_text_special'] = $this->language->get('text_special');
		$this->data['layout_special'] = $this->oc_url('store/product/special');
		$this->data['layout_contact'] = $this->oc_url('store/information/contact');
		$this->data['layout_text_contact'] = $this->language->get('text_contact');
		$this->data['layout_description'] = $this->document->getDescription();
		$this->data['layout_keywords'] = $this->document->getKeywords();
		$this->data['layout_links'] = $this->document->getLinks();
		$this->data['layout_styles'] = $this->document->getStyles();
		$this->data['layout_scripts'] = $this->document->getScripts();
		$this->data['layout_lang'] = $this->language->get('code');
		$this->data['layout_direction'] = $this->language->get('direction');
		$this->data['layout_google_analytics'] = html_entity_decode($this->config->get('config_google_analytics'), ENT_QUOTES, 'UTF-8');
		$this->data['layout_name'] = $this->config->get('config_name');
		$this->data['layout_telephone'] = $this->config->get('config_telephone');
		
		
		
		if ($this->config->get('config_icon') && file_exists(DIR_MER_IMAGE . $this->config->get('config_icon'))) {
			$this->data['layout_icon'] = $server . MERCHANT_DIR.'/' . $this->config->get('config_icon');
		} else {
			$this->data['layout_icon'] = '';
		}
		
		if ($this->config->get('config_logo') && file_exists(DIR_MER_IMAGE . $this->config->get('config_logo'))) {
			$this->data['layout_logo'] = $server .  MERCHANT_DIR.'/'  . $this->config->get('config_logo');
		} else {
			$this->data['layout_logo'] = '';
		}
		
		$this->data['layout_informations'] = array();
		
		foreach ($this->model_catalog_information->getInformations() as $result) {
			if ($result['bottom']) {
				$this->data['layout_informations'][] = array(
						'title' => $result['title'],
						'href'  => $this->oc_url('store/information/information', 'information_id=' . $result['information_id'])
				);
			}
		}
		
		$this->language->load('common/header');
		
		$this->data['layout_text_shopcart'] = $this->language->get('text_shopcart');
		$this->data['layout_text_home'] = $this->language->get('text_home');
		$this->data['layout_text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		$this->data['layout_text_shopping_cart'] = $this->language->get('text_shopping_cart');
		$this->data['layout_text_search'] = $this->language->get('text_search');
		$this->data['layout_text_welcome'] = sprintf($this->language->get('text_welcome'), $this->oc_url('store/account/login', '', 'SSL'), $this->oc_url('store/account/register', '', 'SSL'));
		$this->data['layout_text_logged'] = sprintf($this->language->get('text_logged'), $this->oc_url('store/account/account', '', 'SSL'), $this->customer->getFirstName(), $this->oc_url('store/account/logout', '', 'SSL'));
		$this->data['layout_text_account'] = $this->language->get('text_account');
		$this->data['layout_text_checkout'] = $this->language->get('text_checkout');
		$this->data['layout_text_information'] = $this->language->get('text_information');
		$this->data['layout_text_service'] = $this->language->get('text_service');
		$this->data['layout_text_extra'] = $this->language->get('text_extra');
		$this->data['layout_text_account'] = $this->language->get('text_account');
		$this->data['layout_text_contact'] = $this->language->get('text_contact');
		$this->data['layout_text_return'] = $this->language->get('text_return');
		$this->data['layout_text_sitemap'] = $this->language->get('text_sitemap');
		$this->data['layout_text_manufacturer'] = $this->language->get('text_manufacturer');
		$this->data['layout_text_voucher'] = $this->language->get('text_voucher');
		$this->data['layout_text_affiliate'] = $this->language->get('text_affiliate');
		$this->data['layout_text_special'] = $this->language->get('text_special');
		$this->data['layout_text_account'] = $this->language->get('text_account');
		$this->data['layout_text_order'] = $this->language->get('text_order');
		$this->data['layout_text_wishlist'] = $this->language->get('text_wishlist');
		$this->data['layout_text_newsletter'] = $this->language->get('text_newsletter');
		
		$this->data['layout_url_login'] = $this->oc_url('store/account/login', '', 'SSL');
		$this->data['layout_url_forgot_password'] = $this->oc_url('store/account/forgotten', '', 'SSL');
		$this->data['layout_url_register'] = $this->oc_url('store/account/register', '', 'SSL');
		
		$this->data['layout_home'] = $this->oc_url('store/common/home');
		$this->data['layout_wishlist'] = $this->oc_url('store/account/wishlist', '', 'SSL');
		$this->data['layout_logged'] = $this->customer->isLogged();
		$this->data['layout_account'] = $this->oc_url('store/account/account', '', 'SSL');
		$this->data['layout_shopping_cart'] = $this->oc_url('store/checkout/cart');
		$this->data['layout_checkout'] = $this->oc_url('store/checkout/checkout', '', 'SSL');
		$this->data['layout_contact'] = $this->oc_url('store/information/contact');
		$this->data['layout_return'] = $this->oc_url('store/account/return/insert', '', 'SSL');
		$this->data['layout_sitemap'] = $this->oc_url('store/information/sitemap');
		$this->data['layout_manufacturer'] = $this->oc_url('store/product/manufacturer', '', 'SSL');
		$this->data['layout_voucher'] = $this->oc_url('store/account/voucher', '', 'SSL');
		$this->data['layout_affiliate'] = $this->oc_url('store/affiliate/account', '', 'SSL');
		$this->data['layout_special'] = $this->oc_url('store/product/special');
		$this->data['layout_account'] = $this->oc_url('store/account/account', '', 'SSL');
		$this->data['layout_order'] = $this->oc_url('store/account/order', '', 'SSL');
		$this->data['layout_wishlist'] = $this->oc_url('store/account/wishlist', '', 'SSL');
		$this->data['layout_newsletter'] = $this->oc_url('store/account/newsletter', '', 'SSL');
		$this->data['layout_text_category'] = $this->language->get('text_category');
		// Daniel's robot detector
		$status = true;
		
		if (isset($this->oc_request->server['HTTP_USER_AGENT'])) {
			$robots = explode("\n", trim($this->config->get('config_robots')));
		
			foreach ($robots as $robot) {
				if (strpos($this->oc_request->server['HTTP_USER_AGENT'], trim($robot)) !== false) {
					$status = false;
		
					break;
				}
			}
		}
		
		// A dirty hack to try to set a cookie for the multi-store feature
		/*
		$this->load->model('setting/store');
		
		$this->data['layout_stores'] = array();
		
		if ($this->config->get('config_shared') && $status) {
			$this->data['layout_stores'][] = $server . 'catalog/view/javascript/crossdomain.php?session_id=' . $this->session->getId();
				
			$stores = $this->model_setting_store->getStores();
				
			foreach ($stores as $store) {
				$this->data['layout_stores'][] = $store['url'] . 'catalog/view/javascript/crossdomain.php?session_id=' . $this->session->getId();
			}
		}
		*/
		// Search
		if (isset($this->oc_request->get['search'])) {
			$this->data['layout_search'] = $this->oc_request->get['search'];
		} else {
			$this->data['layout_search'] = '';
		}
		
		// Menu
		// Menu
		if (isset($this->oc_request->get['path'])) {
			$parts = explode('_', (string)$this->oc_request->get['path']);
		} else {
			$parts = array();
		}
		
		if (isset($parts[0])) {
			$this->data['layout_category_id'] = $parts[0];
		} else {
			$this->data['layout_category_id'] = 0;
		}
		
		if (isset($parts[1])) {
			$this->data['layout_child_id'] = $parts[1];
		} else {
			$this->data['layout_child_id'] = 0;
		}
		if (isset($parts[2])) {
			$this->data['layout_ch3_id'] = $parts[2];
		} else {
			$this->data['layout_ch3_id'] = 0;
		}
		
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		
		$this->data['layout_categories'] = array();
			
		$categories = $this->model_catalog_category->getCategories(0);
		
		foreach ($categories as $category) {
			if ($category['top']) {
				// Level 2
				$children_data = array();
		
				$children = $this->model_catalog_category->getCategories($category['category_id']);
		
				foreach ($children as $child) {
					$data = array(
							'filter_category_id'  => $child['category_id'],
							'filter_sub_category' => true
					);
						
					$level3 = $this->model_catalog_category->getCategories($child['category_id']);
					$l3_data = array();
					foreach ($level3 as $l3) {
						$l3_data[] = array(
								'category_id' => $l3['category_id'],
								'name'        => $l3['name'],
								'href'        => $this->oc_url('store/product/category', 'path=' . $category['category_id'] . '_' . $child['category_id']. '_' . $l3['category_id'])
						);
					}
						
					$image = DIR_IMAGE.$child['image'];
					$children_data[] = array(
							'category_id' => $child['category_id'],
							'name'  => $child['name'],
							'href'  => $this->oc_url('store/product/category', 'path=' . $category['category_id'] . '_' . $child['category_id']),
							'thumb' => $image,
							'children3'    => $l3_data
					);
				}
				// Level 1
				$this->data['layout_categories'][] = array(
						'name'     => $category['name'],
						'children' => $children_data,
						'column'   => $category['column'] ? $category['column'] : 1,
						'href'     => $this->oc_url('store/product/category', 'path=' . $category['category_id']),
						'category_id' => $category['category_id']
				);
			}
		}
		
		$this->children = array(
				'module/language',
				'module/currency',
				'module/cart'
		);
		
		$this->setLanguage();
		$this->setCurrency();
		$this->setCart();
		
		
		// header position
		$route = 'store/common/home';
		if (!isset($this->oc_request->get['route'])) {
			$this->data['layout_redirect'] = $this->oc_url('store/common/home');
		} else {
			$data = $this->oc_request->get;
				
			unset($data['_route_']);
				
			$route = $data['route'];
				
			unset($data['route']);
				
			$url = '';
				
			if ($data) {
				$url = '&' . urldecode(http_build_query($data, '', '&'));
			}
				
			$this->data['layout_redirect'] = $this->oc_url($route, $url);
		}
		$layout_id = 0;
		
		if (substr($route, 0, 16) == 'product/category' && isset($this->oc_request->get['path'])) {
			$path = explode('_', (string)$this->oc_request->get['path']);
		
			$layout_id = $this->model_catalog_category->getCategoryLayoutId(end($path));
		}
		
		if (substr($route, 0, 15) == 'product/product' && isset($this->oc_request->get['product_id'])) {
			$layout_id = $this->model_catalog_product->getProductLayoutId($this->oc_request->get['product_id']);
		}
		
		if (substr($route, 0, 23) == 'information/information' && isset($this->oc_request->get['information_id'])) {
			$layout_id = $this->model_catalog_information->getInformationLayoutId($this->oc_request->get['information_id']);
		}
		if ($this->config->get('config_maintenance')) {
			$route = '';
				
			if (isset($this->oc_request->get['route'])) {
				$part = explode('/', $this->oc_request->get['route']);
		
				if (isset($part[0])) {
					$route .= $part[0];
				}
			}
				
			// Show site if logged in as admin
			$this->load->library('user');
				
			$this->user = new User($this->registry);
		
			if (($route != 'payment') && !$this->user->isLogged()) {
				$layout_id = $this->forward('common/maintenance/info');
			}
		}
		if (!$layout_id) {
			$layout_id = $this->model_design_layout->getLayout($route);
		}
		
		if (!$layout_id) {
			$layout_id = $this->config->get('config_layout_id');
		}
		$module_data = array();
		
		$this->load->model('setting/extension');
			
		$extensions = $this->model_setting_extension->getExtensions('module');
			
		foreach ($extensions as $extension) {
			$modules = $this->config->get($extension['code'] . '_module');
				
			if ($modules) {
				foreach ($modules as $module) {
					if ($module['layout_id'] == $layout_id && $module['position'] == 'header' && $module['status']) {
						$module_data[] = array(
								'code'       => $extension['code'],
								'setting'    => $module,
								'sort_order' => $module['sort_order']
						);
					}
				}
			}
		}
			
		$sort_order = array();
			
		foreach ($module_data as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}
			
		array_multisort($sort_order, SORT_ASC, $module_data);
			
		$this->data['layout_modules'] = array();
			
		foreach ($module_data as $module) {
			$module = $this->getChild('Module\\' . ucfirst($module['code']), $module['setting']);
				
			if ($module) {
				$this->data['layout_modules'][] = $module;
			}
		}
		
 
	}
	
	public function setFooter()
	{
		$this->language->load('common/footer');
		
		$this->data['layout_text_information'] = $this->language->get('text_information');
		$this->data['layout_text_service'] = $this->language->get('text_service');
		$this->data['layout_text_extra'] = $this->language->get('text_extra');
		$this->data['layout_text_account'] = $this->language->get('text_account');
		$this->data['layout_text_contact'] = $this->language->get('text_contact');
		$this->data['layout_text_return'] = $this->language->get('text_return');
		$this->data['layout_text_sitemap'] = $this->language->get('text_sitemap');
		$this->data['layout_text_manufacturer'] = $this->language->get('text_manufacturer');
		$this->data['layout_text_voucher'] = $this->language->get('text_voucher');
		$this->data['layout_text_affiliate'] = $this->language->get('text_affiliate');
		$this->data['layout_text_special'] = $this->language->get('text_special');
		$this->data['layout_text_account'] = $this->language->get('text_account');
		$this->data['layout_text_order'] = $this->language->get('text_order');
		$this->data['layout_text_wishlist'] = $this->language->get('text_wishlist');
		$this->data['layout_text_newsletter'] = $this->language->get('text_newsletter');
		$this->data['layout_text_follow'] = $this->language->get('text_follow');
		$this->data['layout_text_support'] = $this->language->get('text_support');
		$this->data['layout_text_twi'] = $this->language->get('text_twi');
		$this->data['layout_text_fb'] = $this->language->get('text_fb');
		$this->data['layout_text_rss'] = $this->language->get('text_rss');
		$this->data['layout_text_yt'] = $this->language->get('text_yt');
		
		$this->load->model('catalog/information');
		
		$this->data['layout_informations'] = array();
		
		foreach ($this->model_catalog_information->getInformations() as $result) {
			if ($result['bottom']) {
				$this->data['layout_informations'][] = array(
						'title' => $result['title'],
						'href'  => $this->oc_url('store/information/information', 'information_id=' . $result['information_id'])
				);
			}
		}
		
		$this->data['layout_contact'] = $this->oc_url('store/information/contact');
		$this->data['layout_return'] = $this->oc_url('store/account/return/insert', '', 'SSL');
		$this->data['layout_sitemap'] = $this->oc_url('store/information/sitemap');
		$this->data['layout_manufacturer'] = $this->oc_url('store/product/manufacturer', '', 'SSL');
		$this->data['layout_voucher'] = $this->oc_url('store/account/voucher', '', 'SSL');
		$this->data['layout_affiliate'] = $this->oc_url('store/affiliate/account', '', 'SSL');
		$this->data['layout_special'] = $this->oc_url('store/product/special');
		$this->data['layout_account'] = $this->oc_url('store/account/account', '', 'SSL');
		$this->data['layout_order'] = $this->oc_url('store/account/order', '', 'SSL');
		$this->data['layout_wishlist'] = $this->oc_url('store/account/wishlist', '', 'SSL');
		$this->data['layout_newsletter'] = $this->oc_url('store/account/newsletter', '', 'SSL');
		$this->data['layout_address'] = nl2br($this->config->get('config_address'));
		$this->data['layout_telephone'] = $this->config->get('config_telephone');
		$this->data['layout_fax'] = $this->config->get('config_fax');
		
		$this->data['layout_powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));
		
		// Whos Online
		if ($this->config->get('config_customer_online')) {
			$this->load->model('tool/online');
		
			if (isset($this->oc_request->server['REMOTE_ADDR'])) {
				$ip = $this->oc_request->server['REMOTE_ADDR'];
			} else {
				$ip = '';
			}
				
			if (isset($this->oc_request->server['HTTP_HOST']) && isset($this->oc_request->server['REQUEST_URI'])) {
				$url = 'http://' . $this->oc_request->server['HTTP_HOST'] . $this->oc_request->server['REQUEST_URI'];
			} else {
				$url = '';
			}
				
			if (isset($this->oc_request->server['HTTP_REFERER'])) {
				$referer = $this->oc_request->server['HTTP_REFERER'];
			} else {
				$referer = '';
			}
		
			$this->model_tool_online->whosonline($ip, $this->customer->getId(), $url, $referer);
		}
 
		
	}
	
	public function setContentTop()
	{
		$this->load->model('design/layout');
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('catalog/information');
		
		if (!empty($this->getRoute())) {
			$route = (string)$this->getRoute();
		} else {
			$route = 'common/home';
		}
		
		$layout_id = 0;
		
		if ($route == 'product/category' && isset($this->oc_request->get['path'])) {
			$path = explode('_', (string)$this->oc_request->get['path']);
		
			$layout_id = $this->model_catalog_category->getCategoryLayoutId(end($path));
		}
		
		if ($route == 'product/product' && isset($this->oc_request->get['product_id'])) {
			$layout_id = $this->model_catalog_product->getProductLayoutId($this->oc_request->get['product_id']);
		}
		
		if ($route == 'information/information' && isset($this->oc_request->get['information_id'])) {
			$layout_id = $this->model_catalog_information->getInformationLayoutId($this->oc_request->get['information_id']);
		}
		
		if (!$layout_id) {
			$layout_id = $this->model_design_layout->getLayout($route);
		}
		
		if (!$layout_id) {
			$layout_id = $this->config->get('config_layout_id');
		}
		
		$module_data = array();
		
		$this->load->model('setting/extension');
		
		$extensions = $this->model_setting_extension->getExtensions('module');
		
		foreach ($extensions as $extension) {
			$modules = $this->config->get($extension['code'] . '_module');
		
			if ($modules) {
				foreach ($modules as $module) {
					if ($module['layout_id'] == $layout_id && $module['position'] == 'content_top' && $module['status']) {
						$module_data[] = array(
								'code'       => $extension['code'],
								'setting'    => $module,
								'sort_order' => $module['sort_order']
						);
					}
				}
			}
		}
		
		$sort_order = array();
		
		foreach ($module_data as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}
		
		array_multisort($sort_order, SORT_ASC, $module_data);
		
		$this->data['layout_modules'] = array();
		$modules= array();
		foreach ($module_data as $module) {
			$module = $this->getChild('Module\\' . ucfirst($module['code']), $module['setting']);
		
			if ($module) {
				$this->data['layout_modules'][] = $module;
				$modules[] = $module;
			}
		}
		
		$viewModel = new ViewModel(array('modules'=>$modules));
		$viewModel->setTemplate('default/template/common/content_top');
		return $viewModel;
		
	}
	public function setContentBottom()
	{
		$this->load->model('design/layout');
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('catalog/information');
		
		if (!empty($this->getRoute())) {
			$route = (string)$this->getRoute();
		} else {
			$route = 'common/home';
		}
		
		$layout_id = 0;
		
		if ($route == 'product/category' && isset($this->oc_request->get['path'])) {
			$path = explode('_', (string)$this->oc_request->get['path']);
		
			$layout_id = $this->model_catalog_category->getCategoryLayoutId(end($path));
		}
		
		if ($route == 'product/product' && isset($this->oc_request->get['product_id'])) {
			$layout_id = $this->model_catalog_product->getProductLayoutId($this->oc_request->get['product_id']);
		}
		
		if ($route == 'information/information' && isset($this->oc_request->get['information_id'])) {
			$layout_id = $this->model_catalog_information->getInformationLayoutId($this->oc_request->get['information_id']);
		}
		
		if (!$layout_id) {
			$layout_id = $this->model_design_layout->getLayout($route);
		}
		
		if (!$layout_id) {
			$layout_id = $this->config->get('config_layout_id');
		}
		
		$module_data = array();
		
		$this->load->model('setting/extension');
		
		$extensions = $this->model_setting_extension->getExtensions('module');
		
		foreach ($extensions as $extension) {
			$modules = $this->config->get($extension['code'] . '_module');
		
			if ($modules) {
				foreach ($modules as $module) {
					if ($module['layout_id'] == $layout_id && $module['position'] == 'content_bottom' && $module['status']) {
						$module_data[] = array(
								'code'       => $extension['code'],
								'setting'    => $module,
								'sort_order' => $module['sort_order']
						);
					}
				}
			}
		}
		
		$sort_order = array();
		
		foreach ($module_data as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}
		
		array_multisort($sort_order, SORT_ASC, $module_data);
		
		$this->data['layout_modules'] = array();
		$modules = array();
		foreach ($module_data as $module) {
			$module = $this->getChild('Module\\' . ucfirst($module['code']), $module['setting']);
		
			if ($module) {
				$this->data['layout_modules'][] = $module;
				$modules[] = $module;
			}
		}
		
		$viewModel = new ViewModel(array('modules'=>$modules));
		$viewModel->setTemplate('default/template/common/content_bottom');
		return $viewModel;
 
	}
	
	public function setColumnLeft()
	{
		$this->load->model('design/layout');
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('catalog/information');
		
		if (!empty($this->getRoute())) {
			$route = (string)$this->getRoute();
		} else {
			$route = 'common/home';
		}
		
		$layout_id = 0;
		
		if ($route == 'product/category' && isset($this->oc_request->get['path'])) {
			$path = explode('_', (string)$this->oc_request->get['path']);
		
			$layout_id = $this->model_catalog_category->getCategoryLayoutId(end($path));
		}
		
		if ($route == 'product/product' && isset($this->oc_request->get['product_id'])) {
			$layout_id = $this->model_catalog_product->getProductLayoutId($this->oc_request->get['product_id']);
		}
		
		if ($route == 'information/information' && isset($this->oc_request->get['information_id'])) {
			$layout_id = $this->model_catalog_information->getInformationLayoutId($this->oc_request->get['information_id']);
		}
		
		if (!$layout_id) {
			$layout_id = $this->model_design_layout->getLayout($route);
		}
		
		if (!$layout_id) {
			$layout_id = $this->config->get('config_layout_id');
		}
		
		$module_data = array();
		
		$this->load->model('setting/extension');
		
		$extensions = $this->model_setting_extension->getExtensions('module');
		
		foreach ($extensions as $extension) {
			$modules = $this->config->get($extension['code'] . '_module');
		
			if ($modules) {
				foreach ($modules as $module) {
					if ($module['layout_id'] == $layout_id && $module['position'] == 'column_left' && $module['status']) {
						$module_data[] = array(
								'code'       => $extension['code'],
								'setting'    => $module,
								'sort_order' => $module['sort_order']
						);
					}
				}
			}
		}
		
		$sort_order = array();
		
		foreach ($module_data as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}
		
		array_multisort($sort_order, SORT_ASC, $module_data);
		
		$this->data['layout_modules'] = array();
		$modules = array();
		foreach ($module_data as $module) {
			$module = $this->getChild('Module\\' . ucfirst($module['code']), $module['setting']);
		
			if ($module) {
				$this->data['layout_modules'][] = $module;
				$modules[]=$module;
			}
		}
		
 		$viewModel = new ViewModel(array('modules'=>$modules));
 		$viewModel->setTemplate('default/template/common/column_left');
 		return $viewModel;
		
	}
	
	public function setColumnRight()
	{
		$this->load->model('design/layout');
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('catalog/information');
		
			if (!empty($this->getRoute())) {
			$route = (string)$this->getRoute();
		} else {
			$route = 'common/home';
		}
		
		$layout_id = 0;
		
		if ($route == 'product/category' && isset($this->oc_request->get['path'])) {
			$path = explode('_', (string)$this->oc_request->get['path']);
		
			$layout_id = $this->model_catalog_category->getCategoryLayoutId(end($path));
		}
		
		if ($route == 'product/product' && isset($this->oc_request->get['product_id'])) {
			$layout_id = $this->model_catalog_product->getProductLayoutId($this->oc_request->get['product_id']);
		}
		
		if ($route == 'information/information' && isset($this->oc_request->get['information_id'])) {
			$layout_id = $this->model_catalog_information->getInformationLayoutId($this->oc_request->get['information_id']);
		}
		
		if (!$layout_id) {
			$layout_id = $this->model_design_layout->getLayout($route);
		}
		
		if (!$layout_id) {
			$layout_id = $this->config->get('config_layout_id');
		}
		
		$module_data = array();
		
		$this->load->model('setting/extension');
		
		$extensions = $this->model_setting_extension->getExtensions('module');
		
		foreach ($extensions as $extension) {
			$modules = $this->config->get($extension['code'] . '_module');
		
			if ($modules) {
				foreach ($modules as $module) {
					if ($module['layout_id'] == $layout_id && $module['position'] == 'column_right' && $module['status']) {
						$module_data[] = array(
								'code'       => $extension['code'],
								'setting'    => $module,
								'sort_order' => $module['sort_order']
						);
					}
				}
			}
		}
		
		$sort_order = array();
		
		foreach ($module_data as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}
		
		array_multisort($sort_order, SORT_ASC, $module_data);
		
		$this->data['layout_modules'] = array();
		$modules = array();
		foreach ($module_data as $module) {
			$module = $this->getChild('Module\\' . ucfirst($module['code']), $module['setting']);
		
			if ($module) {
				$this->data['layout_modules'][] = $module;
				$modules[]=$module;
			}
		}
		
		$viewModel = new ViewModel(array('modules'=>$modules));
		$viewModel->setTemplate('default/template/common/column_right');
		return $viewModel;
		
	}
	
	public function setLanguage()
	{
		if (isset($this->oc_request->post['language_code'])) {
			$this->session->data['language'] = $this->oc_request->post['language_code'];
		
			if (isset($this->oc_request->post['redirect'])) {
				$this->redirect($this->oc_request->post['redirect']);
			} else {
				$this->redirect($this->oc_url('store/common/home'));
			}
		}
		
		$this->language->load('module/language');
		
		$this->data['layout_text_language'] = $this->language->get('text_language');
		$this->data['layout_text_language1'] = $this->session->data['language'];
		
		
		if (isset($this->oc_request->server['HTTPS']) && (($this->oc_request->server['HTTPS'] == 'on') || ($this->oc_request->server['HTTPS'] == '1'))) {
			$connection = 'SSL';
		} else {
			$connection = 'NONSSL';
		}
			
		$this->data['layout_action'] = $this->oc_url('store/module/language', '', $connection);
		
		$this->data['layout_language_code'] = $this->session->data['language'];
		
		$this->load->model('localisation/language');
		
		$this->data['layout_languages'] = array();
		
		$results = $this->model_localisation_language->getLanguages();
		
		foreach ($results as $result) {
			if ($result['status']) {
				$this->data['layout_languages'][] = array(
						'name'  => $result['name'],
						'code'  => $result['code'],
						'image' => $result['image']
				);
			}
		}
		
		if (!isset($this->oc_request->get['route'])) {
			$this->data['layout_redirect'] = $this->oc_url('store/common/home');
		} else {
			$data = $this->oc_request->get;
				
			unset($data['_route_']);
				
			$route = $data['route'];
				
			unset($data['route']);
				
			$url = '';
				
			if ($data) {
				$url = '&' . urldecode(http_build_query($data, '', '&'));
			}
				
			$this->data['layout_redirect'] = $this->url->link($route, $url, $connection);
		}
		/*
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/language.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/language.tpl';
		} else {
			$this->template = 'default/template/module/language.tpl';
		}
		*/
	}
	
	public function setCurrency()
	{
		if (isset($this->oc_request->post['currency_code'])) {
			$this->currency->set($this->oc_request->post['currency_code']);
		
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
		
			if (isset($this->oc_request->post['redirect'])) {
				$this->redirect($this->oc_request->post['redirect']);
			} else {
				$this->redirect($this->oc_url('store/common/home'));
			}
		}
		
		$this->language->load('module/currency');
		
		$this->data['layout_text_currency'] = $this->language->get('text_currency');
		
		if (isset($this->oc_request->server['HTTPS']) && (($this->oc_request->server['HTTPS'] == 'on') || ($this->oc_request->server['HTTPS'] == '1'))) {
			$connection = 'SSL';
		} else {
			$connection = 'NONSSL';
		}
		
		$this->data['layout_action'] = $this->oc_url('store/module/currency', '', $connection);
		
		$this->data['layout_currency_code'] = $this->currency->getCode();
		
		$this->load->model('localisation/currency');
		
		$this->data['layout_currencies'] = array();
		
		$results = $this->model_localisation_currency->getCurrencies();
		
		foreach ($results as $result) {
			if ($result['status']) {
				$this->data['layout_currencies'][] = array(
						'title'        => $result['title'],
						'code'         => $result['code'],
						'symbol_left'  => $result['symbol_left'],
						'symbol_right' => $result['symbol_right']
				);
			}
		}
		
		if (!isset($this->oc_request->get['route'])) {
			$this->data['layout_redirect'] = $this->oc_url('store/common/home');
		} else {
			$data = $this->oc_request->get;
		
			unset($data['_route_']);
		
			$route = $data['route'];
		
			unset($data['route']);
		
			$url = '';
		
			if ($data) {
				$url = '&' . urldecode(http_build_query($data, '', '&'));
			}
		
			$this->data['layout_redirect'] = $this->url->link($route, $url, $connection);
		}
		/*
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/currency.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/currency.tpl';
		} else {
			$this->template = 'default/template/module/currency.tpl';
		}
		*/
	}
	
	public function setCart()
	{
		$this->language->load('module/cart');
		
		if (isset($this->oc_request->get['remove'])) {
			$this->cart->remove($this->oc_request->get['remove']);
				
			unset($this->session->data['vouchers'][$this->oc_request->get['remove']]);
		}
			
		// Totals
		$this->load->model('setting/extension');
		
		$total_data = array();
		$total = 0;
		$taxes = $this->cart->getTaxes();
		
		// Display prices
		if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
			$sort_order = array();
				
			$results = $this->model_setting_extension->getExtensions('total');
				
			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
			}
				
			array_multisort($sort_order, SORT_ASC, $results);
				
			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('total/' . $result['code']);
		
					$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
				}
		
				$sort_order = array();
					
				foreach ($total_data as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}
		
				array_multisort($sort_order, SORT_ASC, $total_data);
			}
		}
		
		$this->data['layout_totals'] = $total_data;
		
		$this->data['layout_heading_title'] = $this->language->get('heading_title');
		$this->data['layout_text_latest_added'] = $this->language->get('text_latest_added');
		$this->data['layout_text_items'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total));
		$this->data['layout_text_items2'] = sprintf($this->language->get('text_items2'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total));
		$this->data['layout_text_empty'] = $this->language->get('text_empty');
		$this->data['layout_text_cart'] = $this->language->get('text_cart');
		$this->data['layout_text_checkout'] = $this->language->get('text_checkout');
		
		$this->data['layout_button_remove'] = $this->language->get('button_remove');
		
		$this->load->model('tool/image');
		
		$this->data['layout_products'] = array();
			
		foreach ($this->cart->getProducts() as $product) {
			if ($product['image']) {
				$image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
			} else {
				$image = '';
			}
				
			$option_data = array();
				
			foreach ($product['option'] as $option) {
				if ($option['type'] != 'file') {
					$value = $option['option_value'];
				} else {
					$filename = $this->encryption->decrypt($option['option_value']);
						
					$value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
				}
		
				$option_data[] = array(
						'name'  => $option['name'],
						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
						'type'  => $option['type']
				);
			}
				
			// Display prices
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$price = false;
			}
				
			// Display prices
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$total = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity']);
			} else {
				$total = false;
			}
				
			$this->data['layout_products'][] = array(
					'key'      => $product['key'],
					'thumb'    => $image,
					'name'     => $product['name'],
					'model'    => $product['model'],
					'option'   => $option_data,
					'quantity' => $product['quantity'],
					'price'    => $price,
					'total'    => $total,
					'href'     => $this->oc_url('store/product/product', 'product_id=' . $product['product_id'])
			);
		}
		
		// Gift Voucher
		$this->data['layout_vouchers'] = array();
		
		if (!empty($this->session->data['vouchers'])) {
			foreach ($this->session->data['vouchers'] as $key => $voucher) {
				$this->data['layout_vouchers'][] = array(
						'key'         => $key,
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'])
				);
			}
		}
			
		$this->data['layout_cart'] = $this->oc_url('store/checkout/cart');
		
		$this->data['layout_checkout'] = $this->oc_url('store/checkout/checkout', '', 'SSL');
		/*
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/cart.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/cart.tpl';
		} else {
			$this->template = 'default/template/module/cart.tpl';
		}
		*/
	}
	
	

	protected function categoryModule() {
		$this->language->load('module/category');
	
	
		if (isset($this->oc_request->get['path'])) {
			$parts = explode('_', (string)$this->oc_request->get['path']);
		} else {
			$parts = array();
		}
	
		if (isset($parts[0])) {
			$this->data['category_id'] = $parts[0];
		} else {
			$this->data['category_id'] = 0;
		}
	
		if (isset($parts[1])) {
			$this->data['child_id'] = $parts[1];
		} else {
			$this->data['child_id'] = 0;
		}
		if (isset($parts[2])) {
			$this->data['ch3_id'] = $parts[2];
		} else {
			$this->data['ch3_id'] = 0;
		}
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
	
		$this->data['side_categories'] = array();
			
		$categories = $this->model_catalog_category->getCategories(0);
	
		foreach ($categories as $category) {
			$children_data = array();
	
			$children = $this->model_catalog_category->getCategories($category['category_id']);
	
			foreach ($children as $child) {
					
				$level3 = $this->model_catalog_category->getCategories($child['category_id']);
				$l3_data = array();
				foreach ($level3 as $l3) {
					$data = array(
							'filter_category_id'  => $l3['category_id'],
							'filter_sub_category' => true
					);
					$product_total = $this->model_catalog_product->getTotalProducts($data);
					if ($this->config->get('config_product_count')) {
						$l3_data[] = array(
								'category_id' => $l3['category_id'],
								'name'        => $l3['name']. ' (' . $product_total . ')',
								'href'        => $this->oc_url('store/product/category', 'path=' . $category['category_id'] . '_' . $child['category_id']. '_' . $l3['category_id'])
						);
					} else {
						$l3_data[] = array(
								'category_id' => $l3['category_id'],
								'name'        => $l3['name'],
								'href'        => $this->oc_url('store/product/category', 'path=' . $category['category_id'] . '_' . $child['category_id']. '_' . $l3['category_id'])
						);
					}
				}
				$data1 = array(
						'filter_category_id'  => $child['category_id'],
						'filter_sub_category' => true
				);
				$product_total = $this->model_catalog_product->getTotalProducts($data1);
				if ($this->config->get('config_product_count')) {
					$children_data[] = array(
							'category_id' => $child['category_id'],
							'name'  => $child['name']. ' (' . $product_total . ')',
							'href'  => $this->oc_url('store/product/category', 'path=' . $category['category_id'] . '_' . $child['category_id']),
							'children3'    => $l3_data
					);
				} else {
					$children_data[] = array(
							'category_id' => $child['category_id'],
							'name'  => $child['name'],
							'href'  => $this->oc_url('store/product/category', 'path=' . $category['category_id'] . '_' . $child['category_id']),
							'children3'    => $l3_data
					);
				}
			}
			// Level 1
			$data2 = array(
					'filter_category_id'  => $category['category_id'],
					'filter_sub_category' => true
			);
			$product_total = $this->model_catalog_product->getTotalProducts($data2);
			if ($this->config->get('config_product_count')) {
				$this->data['side_categories'][] = array(
						'name'     => $category['name']. ' (' . $product_total . ')',
						'children' => $children_data,
						'href'     => $this->oc_url('store/product/category', 'path=' . $category['category_id']),
						'category_id' => $category['category_id']
				);
			} else {
				$this->data['side_categories'][] = array(
						'name'     => $category['name'],
						'children' => $children_data,
						'href'     => $this->oc_url('store/product/category', 'path=' . $category['category_id']),
						'category_id' => $category['category_id']
				);
			}
		}
			
			
			
	
	}
	
	protected function getChild($child, $args = array()) {
	
		$view = $this->forward()->dispatch('Store\Controller\\'.$this->uppercase($child), array(
				'action' => 'index',
				'setting' => $args
		));
		
		$viewRender = $this->getServiceLocator()->get('ViewRenderer');
		$html = $viewRender->render($view);
		 
		return $html;
	}
	
	public function uppercase($string) 
	{
		 $string = str_replace('/', '\\', $string);
		
		$array = explode('\\', $string);
		foreach($array as $key=>$value)
		{
			$array[$key]=ucfirst($value);
		}
		
		$string = implode('\\', $array);
		return $string;
		
	}
	
}

?>