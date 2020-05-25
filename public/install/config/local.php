<?php
$dbParams = array(
		'database'  => '[DBNAME]',
		'username'  => '[DBUSERNAME]',
		'password'  => '[DBPASSWORD]',
		'hostname'  => '[DBHOST]'
);

return array(
		'db' => array(
                'hostname'=> $dbParams['hostname'],
                'dbname'=> $dbParams['database'],
				'username' => $dbParams['username'],
                'password' => $dbParams['password'],
		          'dsn' => 'mysql:dbname='.$dbParams['database'].';host='.$dbParams['hostname'].'',
                ),
    'service_manager' => array(
    		'factories' => array(
                'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
    					
    		),
    ),
);

