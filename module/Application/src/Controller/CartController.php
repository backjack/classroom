<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/11/2018
 * Time: 1:09 PM
 */

namespace Application\Controller;


use Application\Entity\Invoice;
use Application\Entity\Session;
use Application\Model\PaymentMethodTable;
use Intermatics\HelperTrait;
use Zend\Session\Container;

class CartController extends AbstractController {

 use HelperTrait;


    public function indexAction(){

        if(isMobileApp()){
            return $this->redirect()->toRoute('mobile-close');
        }


        $this->data['pageTitle'] = __('Your Cart');

        $this->data['cart'] = getCart();
        if($this->request->isPost()){
            $discount = $this->request->getPost('code');

            $this->data['message'] = getCart()->applyDiscount($discount);
        }

        $currency = currentCurrency()->currency_id;

        $paymentMethodTable = new PaymentMethodTable();
        $this->data['paymentMethods'] = $paymentMethodTable->getMethodsForCurrency($currency);
        if($this->studentIsLoggedIn()){
            $this->layout('layout/student');
        }
        $this->data['loggedIn'] = $this->studentIsLoggedIn();
        return $this->data;
    }

    public function setsessionAction()
    {

        $id = $this->params('id');

        //check if session requires payment

        if(!$this->canEnrollToSession($id)){
            $this->goBack();
        }

        //check if requires payment
        $row = Session::find($id);




        if( (empty($row->payment_required) || $row->amount==0 ) && (empty($row->enrollment_closes) || $row->enrollment_closes > time())  && !empty($row->session_status))
        {

            return $this->redirect()->toRoute('application/default',['controller'=>'student','action'=>'setsession','id'=>$id]);
        }


        $cart = getCart();
        $cart->addSession($id);
        return $this->redirect()->toRoute('cart');
    }

    public function removeAction(){
        $id = $this->params('id');
        $cart = getCart();
        $cart->removeSession($id);
        return $this->goBack();
    }

    public function removecouponAction(){
        getCart()->removeDiscount();
        return $this->goBack();
    }

    public function checkoutAction(){

        if($this->request->isPost()){

            $cart = getCart();
            $method = $this->request->getPost('payment_method');
            $cart->setPaymentMethod($method);

            //now redirect to payment page
            return $this->redirect()->toRoute('application/default',['controller'=>'payment','action'=>'method']);
        }
        else{
            return $this->redirect()->toRoute('cart');
        }

    }

    public function clearAction(){
        getCart()->clear();
        $session = new Container('client');
        if($session->type=='mobile'){
            return $this->redirect()->toRoute('mobile-close');
        }
        else{
            return $this->redirect()->toRoute('home');
        }

    }


}