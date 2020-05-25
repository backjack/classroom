<?php

namespace Intermatics;

class DbConfig {
	function __construct() {
	}
	
	public function get($option)
	{
		switch ($option)
		{
			case 'config_language_id':
				return 1;
				break;
			case 'config_admin_limit':
				return 20;
				break;
			case 'config_file_extension_allowed':
				return file_get_contents('data/configs/allowedfiles.txt');
				break;
			case 'config_file_mime_allowed':
				return file_get_contents('data/configs/allowedfiletypes.txt');
				break;
		}
	}
}

?>