<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 1/24/2017
 * Time: 12:28 PM
 */

namespace Application\Controller;


use Application\Entity\Invoice;
use Application\Entity\PaymentMethodCurrency;
use Application\Model\PaymentMethodFieldTable;
use Application\Model\PaymentMethodTable;
use Application\Model\SessionTable;
use Intermatics\HelperTrait;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;

class PaymentController extends AbstractController {

    use HelperTrait;

    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);
        $controller = $this;
        $events->attach('dispatch', function ($e) use ($controller) {
            $controller->layout('layout/student');
        }, 100);
    }

    /**
     * @return array|\Zend\Http\Response
     * @throws \Exception
     * This loads the cart/payment page where the user can select
     * a payment method
     */
    public function indexAction()
    {
        return $this->redirect()->toRoute('cart');

        $output = [];
        $session= new Container('enroll');
        $id = $session->id;
        if(empty($id))
        {
            return $this->goBack();
        }
        $sessionTable = new SessionTable($this->getServiceLocator());
        $output['row'] = $sessionTable->getRecord($id);

        $paymentMethodsTable = new PaymentMethodTable($this->getServiceLocator());
        $output['methods'] = $paymentMethodsTable->getInstalledMethods();
        $output['pageTitle']= __('Make Payment');
        return $output;
    }

    /**
     * @return mixed|\Zend\Http\Response
     * The forwards to the selected payment method
     * which was supplied via POST
     */
    public function methodoldAction()
    {
        $fieldsTable = new PaymentMethodFieldTable($this->getServiceLocator());
        $session= new Container('enroll');
        $id = $session->id;
        if(empty($id))
        {
            return $this->goBack();
        }
        if($this->request->isPost())
        {
            $code = $this->request->getPost('code');

            if(empty($code))
            {
                return $this->redirect()->toRoute('application/payment');
            }

            $viewModel = $this->forward()->dispatch('Application\Controller\Method',['action'=>$code,'id'=>$id]);
            return $viewModel;
        }
        else{
            return $this->goBack();
        }

    }

    public function methodAction()
    {
        $cart = getCart();

        //check if cart requires payment
        if(!$cart->requiresPayment()){
            $total = $cart->approve($this->getId());
            $this->flashMessenger()->addMessage(__("you-enrolled",['total'=>$total]));
            return $this->redirect()->toRoute('application/default',['controller'=>'student','action'=>'mysessions']);
        }



        if(!$cart->hasItems() || !$cart->getPaymentMethod())
        {
            return $this->redirect()->toRoute('cart');
        }

        //validate the currency of the payment method
        $currency = currentCurrency();
        $method = $cart->getPaymentMethod();
        if($method->is_global == 0 && PaymentMethodCurrency::where('payment_method_id',$method->payment_method_id)->where('currency_id',$currency->currency_id)->count()==0){
            return $this->redirect()->toRoute('cart');
        }


            $code = $cart->getPaymentMethod()->code;

        if(!$cart->hasInvoice()){
            //create invoice
            $invoice = Invoice::create([
                'student_id'=>$this->getId(),
                'currency_id'=>currentCurrency()->currency_id,
                'created_on'=>time(),
                'amount'=>priceRaw($cart->getCurrentTotal()),
                'cart' => serialize($cart),
                'paid'=> 0,
                'payment_method_id'=>$cart->getPaymentMethod()->payment_method_id
            ]);
            $cart->setInvoice($invoice->invoice_id);
        }
        else{
            $invoice = Invoice::find($cart->getInvoice());
            $invoice->amount = priceRaw($cart->getCurrentTotal());
            $invoice->payment_method_id = $cart->getPaymentMethod()->payment_method_id;
            $invoice->cart = serialize($cart);
            $invoice->currency_id = currentCurrency()->currency_id;
            $invoice->save();
        }


            $viewModel = $this->forward()->dispatch('Application\Controller\Method',['action'=>$code]);
            return $viewModel;


    }




}