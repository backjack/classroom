<?php

/**
 * Application\View\Helper
 * 
 * @author
 * @version 
 */
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Application\Model\ArticlesTable;

use Zend\ServiceManager\ServiceLocatorInterface;
/**
 * View Helper
 */
class GetArticle extends AbstractViewHelper  {

	public function __invoke($in) {
		
		// TODO Auto-generated GetArticle::__invoke
		$sm = $this->getServicelocator();
	 
		$articlesTable = new ArticlesTable($sm);
		$row = $articlesTable->tableGateway->select(array('alias'=>trim($in)))->current();
		
		return $row;
	}
	


}
