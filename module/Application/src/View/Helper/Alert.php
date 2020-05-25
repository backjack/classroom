<?php

/**
 * Application\View\Helper
 * 
 * @author
 * @version 
 */
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Session\Container;

/**
 * View Helper
 */
class Alert extends AbstractViewHelper {
	public function __invoke($message,$type='info') {
		// TODO Auto-generated Alert::__invoke
		$html = '';
		
		//set messages from session if present
		$session= new Container('controller');
		 
		if (isset($session->data['success'] )) {
		  
			$message = $session->data['success'];
			 
			$type='success';
			//unset($session->data['success']);
			
		}
		 
		 
		
		if (empty($type)) {
			$type = 'info';
		}
		$message = html_entity_decode($message);
		switch ($type)
		{
			case 'info':
				$html = <<<EOD
				<div class="alert alert-info">
									<a class="close" data-dismiss="alert" href="messages#">&times;</a>
									<h4 class="alert-heading">Info!</h4>
									<p>
									$message
									</p>
								 
								</div>
EOD;
			break;
			case 'error':
				$html = <<<EOD
				
				<div class="alert alert-danger">
                                            <a class="close" data-dismiss="alert" href="messages#">&times;</a>
                                            <h4 class="alert-heading">Error!</h4>
                                            <p>
                                                  $message 
                                            </p>
                                         
                                        </div>
EOD;
		   break;
			case 'success':
				$html=<<<EOD
		<div class="alert alert-success">
									<a class="close" data-dismiss="alert" href="messages#">&times;</a>
									<h4 class="alert-heading">Success!</h4>
									<p>
										$message 
									</p>
									 
								</div>				
EOD;
		break;
		default:
			$html = <<<EOD
				<div class="alert alert-info">
									<a class="close" data-dismiss="alert" href="messages#">&times;</a>
									<h4 class="alert-heading">Info!</h4>
									<p>
									$message
									</p>
					
								</div>
EOD;
			
			
		}
		
		 
		if(!empty($message))
		{
			return $html;
		}
		else {
			return '';
		}
	}
}
