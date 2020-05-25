<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

/**
 * List of enabled modules for this application.
 *
 * This should be an array of module namespaces used in the application.
 */
$mode= getenv('APP_MODE');
$modules =  [
    'Zend\Session',
    'Application',
    'Zend\Serializer',
    'Zend\Cache',
    'Zend\Navigation',
    'Zend\Mvc\Plugin\FlashMessenger',
    'Zend\Paginator',
    'Zend\Form',
    'Zend\Db',
    'Zend\Router',
    'Zend\Validator',
    'BjyAuthorize',
    'Admin',
    'Intermatics',
    'SanCaptcha'
];


if($mode=='demo'){

    array_unshift($modules,'WhoopsErrorHandler');
}
return $modules;
