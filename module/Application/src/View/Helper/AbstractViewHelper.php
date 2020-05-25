<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 3/1/2018
 * Time: 7:52 PM
 */

namespace Application\View\Helper;


use Zend\View\Helper\AbstractHelper;

class AbstractViewHelper extends AbstractHelper {

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    public function __construct($container)
    {

        $this->setServiceManager($container);
    }

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * @param mixed $serviceManager
     * @return $this
     */
    public function setServiceManager($serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    public function getServiceLocator(){
        return $this->serviceManager;
    }

}