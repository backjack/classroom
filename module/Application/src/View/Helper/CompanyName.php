<?php

/**
 * Application\View\Helper
 * 
 * @author
 * @version 
 */
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Config\Reader\Xml;

/**
 * View Helper
 */
class CompanyName extends AbstractViewHelper {
	public function __invoke() {
		// TODO Auto-generated CompanyName::__invoke
		$reader = new Xml();
		$data   = $reader->fromFile('config/custom/global.xml');
		$name = $data['companyname'];
		return $name;
	}
}
