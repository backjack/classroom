<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 3/1/2018
 * Time: 8:01 PM
 */

namespace Application\View\Helper;

use Zend\ServiceManager\AbstractPluginManager;


class ViewHelperFactory  {


    public function __invoke($container, $requestedName, array $options = null)
    {

        $service = (null === $options) ? new $requestedName($container) : new $requestedName($container,$options);

        return $service->setServiceManager($container);
    }
/*
    public function __invoke($container)
    {

        if (! $container instanceof AbstractPluginManager) {
            // zend-servicemanager v3. v2 passes the helper manager directly.
            $container = $container->get('ViewHelperManager');
        }

        return new GetSetting($container);
    }
*/

}