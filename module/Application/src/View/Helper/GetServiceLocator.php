<?php

/**
 * Application\View\Helper
 * 
 * @author
 * @version 
 */
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;


use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * View Helper
 */
class GetServiceLocator extends AbstractViewHelper   {

	public function __invoke() {
		// TODO Auto-generated GetServiceLocator::__invoke
		$sm = $this->getServicelocator();
		
		return $sm;
	}

}
