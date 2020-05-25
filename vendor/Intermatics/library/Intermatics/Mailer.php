<?php

namespace Intermatics;

class Mailer {
	
	protected $senderEmail;
	protected $recipientEmail;
	protected $senderName;
	protected $recipientName;
	protected $subject;
	protected $message;
	
	
	
	function __construct() {
	}
	
	function sendToEmail($email,$subject,$prameters,$template='default')
	{
		
	}
}

?>