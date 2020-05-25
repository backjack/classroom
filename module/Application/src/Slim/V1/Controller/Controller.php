<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 11/22/2018
 * Time: 10:53 AM
 */

namespace Application\Slim\V1\Controller;
use Application\Entity\Student;
use Intermatics\HelperTrait;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

abstract class Controller {

    protected $container;

    use HelperTrait;
    // constructor receives container instance
    public function __construct(ContainerInterface $container) {
        $this->container = $container;

    }

    public function getApiStudent(){
        $authToken = $this->container->get('request')->getHeaderLine('Authorization');
        $student = Student::where('api_token',$authToken)->first();
        if($student){
            return $student;
        }
        else{
            return false;
        }
    }

    public function getServiceLocator(){
        return $GLOBALS['serviceManager'];
    }

    public function getBaseApiUrl($request){
        $uri = $request->getUri();
        $baseUrl = $uri->getBaseUrl();
        return $baseUrl;
    }

    public function validateParams($data,$rules){

        $status = $this->validate($data,$rules);
        if(!$status){
            return jsonResponse(['status'=>false,'msg'=>$this->getValidationErrors()]);
        }

    }
}