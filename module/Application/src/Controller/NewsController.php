<?php

namespace Application\Controller;

use Intermatics\HelperTrait;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel; 
use Application\Model\NewsflashTable; 
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

    use HelperTrait;
    /**
     * @return ViewModel
     * Displays all blog posts
     */
	public function indexAction() {
	 
		// TODO Auto-generated NewssController::indexAction() default action
		$table = new NewsflashTable($this->getServiceLocator());
	
		$paginator = $table->getPaginatedRecords(true);
	
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
		$paginator->setItemCountPerPage(30);
		return $this->getViewModel (array(
				'paginator'=>$paginator,
				'pageTitle'=>__('Blog'),
		));
	
		 
	}

    /**
     * @return array
     * @throws \Exception
     * View a blog post
     */
	public function viewAction()
	{
		$id =$this->params('id');
		$newsTable = new NewsflashTable($this->getServiceLocator());
		$row = $newsTable->getRecord($id);
		return $this->getViewModel(['row'=>$row,'pageTitle'=>__('Blog Post').': '.$row->title]);
	}

    /**
     * @return string
     * Returns a the site base url
     */
	public function getBaseUrl()
	{
		$event = $this->getEvent();
		$request = $event->getRequest();
		$router = $event->getRouter();
		$uri = $router->getRequestUri();
		$baseUrl = sprintf('%s://%s%s', $uri->getScheme(), $uri->getHost(), $request->getBaseUrl());
		return $baseUrl;
	}
	
	
	 
	
	
}