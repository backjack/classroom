<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 3/1/2018
 * Time: 11:03 AM
 */

namespace Application\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Model\ViewModel;

class AbstractController extends AbstractActionController
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;
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

    public function bladeView($view,$data){
        $factory = $this->getServiceManager()->get('BladeFactory');

        $data= array_merge($data,$this->viewHelpers());


        $bladeHtml = $factory->make($view,$data);
        $data['bladeHtml'] = $bladeHtml;
        $viewModel= new ViewModel($data);
        $viewModel->setTemplate('application/blade');
        return $viewModel;
    }

    public function bladeHtml($view,$data){
        $factory = $this->getServiceManager()->get('BladeFactory');

        $data= array_merge($data,$this->viewHelpers());
        $bladeHtml = $factory->make($view,$data);
        return $bladeHtml;
    }

    public function blade($view,$data){
        $html = $this->bladeHtml($view,$data);
        echo $html;
        return $this->response;
    }

    private function viewHelpers(){
        $data = [];
        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $data['viewHelper'] = $viewHelperManager ;
        $data['_url'] = $viewHelperManager->get('url');
        $data['_headLink'] = $viewHelperManager->get('headLink');
        $data['_headScript'] = $viewHelperManager->get('headScript');
        $data['_basePath'] = $viewHelperManager->get('basePath');
        $data['_headTitle'] = $viewHelperManager->get('headTitle');
        $data['_formElement'] = $viewHelperManager->get('formElement');
        $data['_formLabel'] = $viewHelperManager->get('formLabel');
        return $data;
    }
}