<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/9/2018
 * Time: 2:02 PM
 */
namespace Application\Library;

use Application\Entity\Coupon;
use Application\Entity\CouponInvoice;
use Application\Entity\Invoice;
use Application\Entity\PaymentMethod;
use Application\Entity\Session;
use Application\Entity\SessionToSessionCategory;
use Application\Model\StudentSessionTable;
use Zend\Session\Container;

class Cart {

    private $sessions= [];
    private $isDiscount = false;
    private $couponId = null;
    private $discountApplied;
    private $paymentMethodId;
    private $total;
    private $invoiceId;
    private $studentId;



    public function hasInvoice(){
        if(!empty($this->invoiceId) && Invoice::find($this->invoiceId)){
            return true;
        }
        else{
            return false;
        }
    }

    public function setInvoice($id){
        $this->invoiceId = $id;
        $this->store();
    }

    public function getInvoice(){
        return $this->invoiceId;
    }

    public function getInvoiceObject(){
        return Invoice::find($this->invoiceId);
    }

    public function addSession($sessionId){
        $this->sessions[$sessionId] = $sessionId;
        $this->reapplyDiscount();
        $this->storeTotal();
    }

    public function removeSession($sessionId){
        unset($this->sessions[$sessionId]);
        $this->reapplyDiscount();
        $this->storeTotal();
    }

    public function getSessions(){

        $sessionObjects= [];
        foreach($this->sessions as $id){
            $session = Session::find($id);

            if($session){
                $sessionObjects[$id] = $session;
            }

        }

        return $sessionObjects;
    }

    public function getTotalItems(){
        return count($this->sessions);
    }

    public function getRawTotal(){
        $total = 0;
        foreach($this->getSessions() as $session){
            $total += $session->amount;
        }

        return $total;
    }

    public function getCurrentTotal(){
        $total = 0;
        foreach($this->getSessions() as $session){
            $total += $session->amount;
        }

        if($this->isDiscount){
           // $coupon = Coupon::where('coupon_id',$this->couponId)->where('expires','>',time())->where('enabled',1)->first();
            $coupon = Coupon::find($this->couponId);



            if($coupon){

                //generate two totals. One for discounted items and the other for non discounted items

                $couponSessionsTotal = $coupon->couponSessions()->count();
                $couponCategoriesTotal = $coupon->couponCategories()->count();

                if(empty($couponCategoriesTotal) && empty($couponSessionsTotal) ){
                 $total=   $this->getDiscountedAmount($coupon,$total);
                }
                else{

                    //get list of products to apply discount to
                    $discountedSessions=[];
                    $excludedSessions=[];

                    foreach($this->getSessions() as $session){

                            $sessionId= $session->session_id;

                            //check if item is among list of sessions
                            $count = $coupon->couponSessions()->where('session_id',$sessionId)->count();
                            if(!empty($count)){
                                $discountedSessions[$sessionId] = $session;
                                continue;
                            }

                            //check if item is in list of session categories
                            foreach($coupon->couponCategories as $category){
                                $category= $category->session_category_id;

                                $count = SessionToSessionCategory::where('session_category_id',$category)->where('session_id',$sessionId)->count();
                                if(!empty($count)){
                                    $discountedSessions[$sessionId] = $session;
                                    continue 2;
                                }

                            }

                            if(!isset($discountedSessions[$sessionId])){
                                $excludedSessions[$sessionId] = $session;
                            }


                    }

                    //now generate list both totals
                    $discountedTotal=0;
                    $excludedTotal = 0;

                    foreach($discountedSessions as $session){
                        $discountedTotal += $session->amount;
                    }

                    foreach($excludedSessions as $session){
                        $excludedTotal += $session->amount;
                    }


                    $discountedTotal = $this->getDiscountedAmount($coupon,$discountedTotal);

                    $total = $discountedTotal + $excludedTotal;


                }


            }



        }

        $this->total = $total;
        return $total;
    }


    private function getDiscountedAmount($coupon,$amount){
        if($coupon->type=='F'){
            $total = $amount - $coupon->discount;
            if($total < 0){
                $total=0;
            }
        }
        else{
            $discount = $coupon->discount;
            $discountAmount = $amount * ($discount/100);
            $total = $amount - $discountAmount;
        }

        return $total;
    }

    public function storeTotal(){
        $this->total = $this->getCurrentTotal();
        $this->store();
    }

    public function getStoredTotal(){
        return $this->total;
    }

    public function reapplyDiscount(){
        if($this->hasDiscount()){
            $coupon = Coupon::find($this->couponId);
            $this->applyDiscount($coupon->code);
        }
    }

    public function applyDiscount($code){
      //  $coupon = Coupon::where('code',trim(strtolower($code)))->where('expires','>',time())->where('enabled',1)->first();

        $coupon = $this->getCoupon($code);
        if($coupon){

            $this->couponId = $coupon->coupon_id;
            $this->isDiscount = true;
            $this->discountApplied = $coupon->discount;
            $message = 'Discount code applied';
        }
        else{
            $this->couponId = null;
            $this->isDiscount = false;
            $this->discountApplied = null;
            $message = 'The code you entered is invalid';
        }
        $this->storeTotal();
        return $message;
    }

    public function store(){
        $session = new Container('cart');
        $session->cart = serialize($this);

    }

    public function clear(){
        unset($_SESSION['cart']);
    }

    public function setPaymentMethod($id){
        $this->paymentMethodId = $id;
        $this->storeTotal();
    }

    public function getPaymentMethod(){
        return PaymentMethod::find($this->paymentMethodId);
    }

    public function hasItems(){

        if(count($this->sessions)>0){
            return true;
        }
        else{
            return false;
        }
    }

    public function hasDiscount(){
        return !empty($this->couponId);
    }

    public function getDiscount(){
        return $this->discountApplied;
    }

    public function removeDiscount(){
        $this->isDiscount = false;
        $this->couponId = null;
        $this->discountApplied = null;
        $this->storeTotal();
    }

    public function approve($studentId){
        $count = 0;
        foreach($this->sessions as $session){
            $studentSessionTable = new StudentSessionTable();
            $code = generateRandomString(5);
            $studentSessionTable->addRecord(array(
                'student_id'=>$studentId,
                'session_id'=>$session,
                'reg_code'=>$code,
                'enrolled_on'=>time()
            ));
            $count++;
        }
        if($this->hasInvoice()){
            //update invoice
            $invoice = $this->getInvoiceObject();
            $invoice->paid = 1;
            $invoice->save();

            //save coupon invoice
            if($this->hasDiscount()){
                CouponInvoice::create([
                   'coupon_id'=>$this->couponId,
                    'invoice_id'=>$this->invoiceId
                ]);
            }
        }


        $this->clear();
        return $count;
    }

    public function requiresPayment(){

        $paymentRequired = false;
        foreach($this->getSessions() as $session){
            if(!empty($session->payment_required)){
                $paymentRequired = true;
            }
        }

        if($this->hasDiscount() && $this->getCurrentTotal()==0){
            return false;
        }


        return $paymentRequired;

    }

    public function updateInvoice(){
        if($this->hasInvoice()){
            $invoice = Invoice::find($this->getInvoice());
            $invoice->amount = priceRaw($this->getCurrentTotal());
            $invoice->payment_method_id = $this->getPaymentMethod()->payment_method_id;
            $invoice->cart = serialize($this);
            $invoice->currency_id = currentCurrency()->currency_id;
            $invoice->save();
        }
    }

    public function getCouponId(){
        return $this->couponId;
    }

    public function getCoupon($code){

        $coupon = Coupon::where('code',trim(strtolower($code)))->where('expires','>',time())->where('date_start','<=',time())->where('enabled',1)->first();

        if($coupon){
            //check if coupon has total
            if(!empty($coupon->total))
            {
                $rawTotal = $this->getRawTotal();
                if($rawTotal < $coupon->total){
                    return false;
                }
            }

            //check total uses
            if(!empty($coupon->uses_total)){
                $totalUses = $coupon->couponInvoices()->count();
                if($totalUses  >= $coupon->uses_total){
                    return false;
                }
            }


            //check total uses for this customer
     /*       if($this->studentId && !empty($coupon->uses_customer)){

                $totalCustomerUses = $coupon->couponInvoices()->where('student_id',$this->studentId)->count();

                if($totalCustomerUses >= $coupon->uses_customer){
                    return false;
                }
            }*/


        }

        return $coupon;
    }

    public function setStudent($studentId){
        $this->studentId = $studentId;
    }

    public function discountType(){
        if(!$this->hasDiscount()){
            return null;
        }

        $coupon = Coupon::find($this->couponId);
        if($coupon){
            return $coupon->type;
        }
        else{
            return null;
        }

    }

}