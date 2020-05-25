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
class GetClass extends AbstractViewHelper  {
	public function __invoke($name) {
		// TODO Auto-generated GetClass::__invoke
		
		$sm = $this->getServicelocator();
		$name = '\\'.$name;
		$class = new $name($sm);
		
		
		return $class;
	}

	
	
	
}
