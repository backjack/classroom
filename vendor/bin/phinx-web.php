<?php
$dbParams = $GLOBALS['dbParams'];
return array(
    'paths'=>array(
        'migrations'=>'%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds'=>'%%PHINX_CONFIG_DIR%%/db/seeds'
    ),
    'environments' =>
        array(
            'default_database' => 'development',
            'development' => array(
                'name' => $dbParams['database'],
                'adapter' => 'mysql',
                'host'=>$dbParams['hostname'],
                'user'=>$dbParams['username'],
                'pass'=>$dbParams['password'],
                'port'=>'3306',
                'charset'=>'utf8',
            )
        )
);
?>