<?php

/**
 * Application\View\Helper
 * 
 * @author
 * @version 
 */
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Application\Model\NewsflashTable;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * View Helper
 */
class GetNews extends AbstractViewHelper  {

	public function __invoke($limit) {
		// TODO Auto-generated GetNews::__invoke
		
		$sm = $this->getServicelocator();
		$newsTable = new NewsflashTable($sm);
		$rowset = $newsTable->getNews($limit);
		return $rowset;
	}
	

	
}
