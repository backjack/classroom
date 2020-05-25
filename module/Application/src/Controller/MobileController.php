<?php
namespace Application\Controller;
use Application\Entity\Invoice;
use Application\Entity\Student;
use Intermatics\HelperTrait;
use Intermatics\UtilityFunctions;
use Zend\EventManager\EventManagerInterface;
use Zend\Session\Container;

class MobileController extends AbstractController{

    use HelperTrait;

    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);
        $controller = $this;
        $events->attach('dispatch', function ($e) use ($controller) {
            $controller->layout('layout/mobile');
        }, 100);
    }

    public function loadAction(){

        $token = $this->request->getQuery('token');
        $invoiceId = $this->request->getQuery('invoice');

        //get user with token
        $student = Student::where('api_token',trim($token))->where('token_expires','>',time())->first();
        if(!$student){
            exit('Invalid Token');
        }

        //log student in
        UtilityFunctions::setRole('student');



        $this->getAuthService()->getStorage()->write(array(
            'email'=>trim($student->email),
            'role'=>'student'
        ));

        //create cart
        $invoice= Invoice::find($invoiceId);
        if(!$invoice || ($student->student_id != $invoice->student_id)){
            exit('Invalid invoice');
        }

        $cart =  unserialize($invoice->cart);
        $cart->setInvoice($invoiceId);
        $cart->store();

        $session = new Container('client');
        $session->type = 'mobile';

        //get payment method
        $paymentMethod = $invoice->paymentMethod->code;
        $viewModel = $this->forward()->dispatch('Application\Controller\Method',['action'=>$paymentMethod]);
        $this->layout('layout/mobile');
        return $viewModel;



    }

    public function closeAction(){
        exit('close');
    }


    public function getAuthService()
    {

        return $this->getServiceLocator()->get('StudentAuthService');
    }




}