<?php

namespace Intermatics;
use Merchant\Model\MerchantsTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Merchant\Model\ProductCategoriesTable;
use Merchant\Model\CategoryTable;
use Zend\Config\Reader\Xml;
use Intermatics\Opencart\Library\Language;
use Zend\Session\Container;
use Intermatics\Opencart\Library\Request;
use Merchant\Model\SettingTable;
use Intermatics\Opencart\Library\Image;
use Intermatics\Opencart\Library\Document;

class BaseController extends AbstractActionController {
	
	//opencart compatibility properties
	protected $registry;	
	protected $id;
	protected $layout;
	protected $template;
	protected $children = array();
	protected $data = array();
	protected $output;
	protected $oc_request;
	protected $language;
	protected $languageDir;
	protected $config;
	protected $session;
	protected $document;
	private $error = array();
	public  $load;
	public  $user; 
	
	public function __construct()
	{
		$this->oc_request = new Request();
		$this->language = new Language($this->languageDir.'/english');
		$this->language->load('english');
		
		$this->session = new Container('controller');  
		
		if (!isset($this->session->data)) {
				$this->session->data = array();
		}
	
		$this->session->data['token'] = '';
		$this->load = new Load();
		$this->user = new User();
		$this->document = new Document();
		
	}
	
	protected $acceptCriteria = array(
			'Zend\View\Model\ViewModel' => array(
					'text/html',
			),
			'Zend\View\Model\JsonModel' => array(
					'application/json',
			));
	
	

	
	public function getFileUploadLocation()
	{
		// Fetch Configuration from Module Config
		$config = $this->getServiceLocator()->get('config');
		return $config['module_config']['upload_location'];
	}
	
	public function getCustomConfig()
	{
		$config = $this->getServiceLocator()->get('CustomConfig');
		return $config;
	}
	
	//opencart compatiblity 
	 
  	protected function getChild($child, $args = array()) {
	 
 
	}
	
	protected function hasAction($child, $args = array()) {
 
	}
	
	protected function render() {
	 
	}
	
	public function oc_url($path,$url='',$ssl='')
	{
		$pathArray = explode('/', $path);
		if (!isset($pathArray[3])) {
			$pathArray[3] = 'index';
		}
		
		
		
		$link = $this->url()->fromRoute($pathArray[0].'/'.$pathArray[1],array('controller'=>$pathArray[2],'action'=>$pathArray[3])).'?'.$url;
		return $link;
		
	}
	
	public function oc_redirect($url)
	{
		$this->redirect()->toUrl($url);
	}
	
	public function getBaseUrl()
	{
		$event = $this->getEvent();
		$request = $event->getRequest();
		$router = $event->getRouter();
		$uri = $router->getRequestUri();
		$baseUrl = sprintf('%s://%s%s', $uri->getScheme(), $uri->getHost(), $request->getBaseUrl());
		return $baseUrl;
	}
	
	public function resizeImage($filename, $width, $height) {
		if (!file_exists(DIR_IMAGE . $filename) || !is_file(DIR_IMAGE . $filename)) {
			return;
		}
	
		$info = pathinfo($filename);
	
		$extension = $info['extension'];
	
		$old_image = $filename;
		$new_image = 'cache/' . substr($filename, 0, strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;
	
		if (!file_exists(DIR_IMAGE . $new_image) || (filemtime(DIR_IMAGE . $old_image) > filemtime(DIR_IMAGE . $new_image))) {
			$path = '';
	
			$directories = explode('/', dirname(str_replace('../', '', $new_image)));
	
			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;
	
				if (!file_exists(DIR_IMAGE . $path)) {
					@mkdir(DIR_IMAGE . $path, 0777);
				}
			}
	
			$image = new Image(DIR_IMAGE . $old_image);
			$image->resize($width, $height);
			$image->save(DIR_IMAGE . $new_image);
		}
	
		if (isset($this->oc_request->server['HTTPS']) && (($this->oc_request->server['HTTPS'] == 'on') || ($this->oc_request->server['HTTPS'] == '1'))) {
			return HTTPS_CATALOG . 'image/' . $new_image;
		} else {
			return HTTP_CATALOG . 'image/' . $new_image;
		}
	}
	
	public function resizeMerchantImage($filename, $width, $height) {
		if (!file_exists(DIR_MER_IMAGE . $filename) || !is_file(DIR_MER_IMAGE . $filename)) {
			return;
		}
	
		$info = pathinfo($filename);
	
		$extension = $info['extension'];
	
		$old_image = $filename;
		$new_image = 'cache/' . substr($filename, 0, strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;
	
		if (!file_exists(DIR_MER_IMAGE . $new_image) || (filemtime(DIR_MER_IMAGE . $old_image) > filemtime(DIR_MER_IMAGE . $new_image))) {
			$path = '';
	
			$directories = explode('/', dirname(str_replace('../', '', $new_image)));
	
			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;
	
				if (!file_exists(DIR_MER_IMAGE . $path)) {
					@mkdir(DIR_MER_IMAGE . $path, 0777);
				}
			}
	
			$image = new Image(DIR_MER_IMAGE . $old_image);
			$image->resize($width, $height);
			$image->save(DIR_MER_IMAGE . $new_image);
		}
	
		if (isset($this->oc_request->server['HTTPS']) && (($this->oc_request->server['HTTPS'] == 'on') || ($this->oc_request->server['HTTPS'] == '1'))) {
			return $this->getBaseUrl() . str_replace('public', '', DIR_MER_IMAGE) . $new_image;
		} else {
			return $this->getBaseUrl() . str_replace('public', '', DIR_MER_IMAGE) . $new_image;
		}
	}
	
	
}
 
Class Load{
	
	public function model($var){
		
	}
}

Class User
{
	public function hasPermission($var,$var)
	{
		return true;
	}
}

?>