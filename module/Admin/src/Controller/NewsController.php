<?php

namespace Admin\Controller;

use Application\Controller\AbstractController;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel; 
use Application\Model\NewsflashTable;
use Application\Form\NewsflashForm;
use Application\Form\NewsflashFilter;
use Intermatics\UtilityFunctions;
/**
 * NewsController
 *
 * @author
 *
 * @version
 *
 */
define('DIR_MER_IMAGE', 'public/');
class NewsController extends AbstractController {
	
	
	public function indexAction() {
		// TODO Auto-generated NewssController::indexAction() default action
		$table = new NewsflashTable($this->getServiceLocator());
	
		$paginator = $table->getPaginatedRecords(true);
	
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
		$paginator->setItemCountPerPage(30);
		return new ViewModel (array(
				'paginator'=>$paginator,
				'pageTitle'=>__('Blog Posts'),
		));
	
		 
	}
	
	public function addAction()
	{
		$output = array();
		$newsflashTable = new NewsflashTable($this->getServiceLocator());
		$form = new NewsflashForm();
		$filter = new NewsflashFilter();
	
		if ($this->request->isPost()) {
	
			$form->setInputFilter($filter);
			$data = $this->request->getPost();
			$form->setData($data);
			if ($form->isValid()) {
	
				$array = $form->getData();
				$array['date']=time();
				$array[$newsflashTable->getPrimary()]=0;
				$newsflashTable->saveRecord($array);
				//    $this->flashmessenger()->addMessage(__('Changes Saved!'));
				$output['message'] = __('Record Added!');
				$form = new NewsflashForm(null);
			}
			else{
				$output['message'] = __('save-failed-msg');
				if ($data['picture']) {
					$output['display_image']= resizeImage($data['picture'], 100, 100,$this->getBaseUrl());
				}
			}
	
		}
		else{
		$output['no_image']= resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
		$output['display_image']= resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
		}
		$output['form'] = $form;
		$output['pageTitle']= __('Add Post');
		$output['action']='add';
		$output['id']=null;
		return new ViewModel($output);
	}
	
	public function editAction(){
		$output = array();
		$newsflashTable = new NewsflashTable($this->getServiceLocator());
		$form = new NewsflashForm(null);
		$filter = new NewsflashFilter();
		$id = $this->params('id');
	
		$row = $newsflashTable->getRecord($id);
		if ($this->request->isPost()) {
	
			$form->setInputFilter($filter);
			$data = $this->request->getPost();
			$form->setData($data);
			if ($form->isValid()) {
					
	
	
				$array = $form->getData();
				$array[$newsflashTable->getPrimary()]=$id;
				$newsflashTable->saveRecord($array);
				//    $this->flashmessenger()->addMessage(__('Changes Saved!'));
				$output['message'] = __('Changes Saved!');
				$row = $newsflashTable->getRecord($id);
			}
			else{
				$output['message'] = __('save-failed-msg');
			}
	
		}
		else {
	
			$data = UtilityFunctions::getObjectProperties($row);
		 
			
		
			
			$form->setData($data);
	
		}
		
		if ($row->picture && file_exists(DIR_MER_IMAGE . $row->picture) && is_file(DIR_MER_IMAGE . $row->picture)) {
			$output['display_image'] = resizeImage($row->picture, 100, 100,$this->getBaseUrl());
		} else {
			$output['display_image'] = resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
		}
			
		
		$output['no_image']= $this->getBaseUrl().'/'.resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
		$output['form'] = $form;
		$output['id'] = $id;
		$output['pageTitle']= __('Edit Post');
		$output['row']= $row;
		$output['action']='edit';
	
		$viewModel = new ViewModel($output);
		$viewModel->setTemplate('admin/news/add');
		return $viewModel ;
	
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
	
	
	public function deleteAction()
	{
		$table = new NewsflashTable($this->getServiceLocator());
		$id = $this->params('id');
		$table->deleteRecord($id);
		$this->flashmessenger()->addMessage(__('Record deleted'));
		$this->redirect()->toRoute('admin/default',array('controller'=>'news','action'=>'index'));
	}
	
	
}