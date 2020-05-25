<?php

/**
 * Application\View\Helper
 * 
 * @author
 * @version 
 */
namespace Application\View\Helper;

use Intermatics\UtilityFunctions;
use Zend\View\Helper\AbstractHelper;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * View Helper
 */
class LoggedIn extends AbstractViewHelper    {

	public function __invoke() {
	 
		// TODO Auto-generated LoggedIn::__invoke
	 
		$sm = $this->getServicelocator();
		$authService = $sm->get('StudentAuthService');
        $role = UtilityFunctions::getRole();

		
		return ($authService->hasIdentity() && $role == 'student');
		 
	}
	

}
