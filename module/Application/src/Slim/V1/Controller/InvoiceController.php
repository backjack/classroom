<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 11/22/2018
 * Time: 11:01 AM
 */

namespace Application\Slim\V1\Controller;

use Application\Entity\Coupon;
use Application\Entity\Currency;
use Application\Entity\Invoice;
use Application\Entity\PaymentMethod;
use Application\Library\Cart;
use Application\Model\PaymentMethodTable;
use Intermatics\HelperTrait;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class InvoiceController extends Controller  {

    use HelperTrait;

    public function invoices(){

    }

    public function paymentMethods(Request $request,Response $response,$args){

        $params = $request->getQueryParams();
        $isValid = $this->validate($params,[
            'currency_id'=>'required'
        ]);

        if(!$isValid){
            return jsonResponse(['status'=>false,'msg'=>$this->getValidationErrors()]);
        }

        $params = $request->getQueryParams();

        $currencyId = $params['currency_id'];
        $paymentMethodTable = new PaymentMethodTable();
        $paymentMethods = $paymentMethodTable->getMethodsForCurrency($currencyId);
        $output = $paymentMethods->toArray();
        return jsonResponse($output);
    }

    public function storeInvoice(Request $request,Response $response,$args){

        $data = $request->getParsedBody();
        $isValid = $this->validate($data,[
            'sessions'=>'required',
            'payment_method_id'=>'required',
            'currency_id'=>'required'

        ]);

        if(!$isValid){
            return jsonResponse(['status'=>false,'msg'=>$this->getValidationErrors()]);
        }

        //create cart
        $cart = new Cart();
      //  $cart->setStudent($this->getApiStudentId());

        //add items to cart
        foreach($data['sessions'] as $value){
            $cart->addSession($value);
        }

        //now create the invoice
        //first check if the student has an unpaid invoice recently created
        $student = $this->getApiStudent();
        $studentId = $student->student_id;
        $currencyId = $data['currency_id'];

        if(!$cart->requiresPayment()){
            $cart->approve($studentId);
            return jsonResponse([
                'payment_required'=> $cart->requiresPayment(),
                'status'=>true,
            ]);

        }


        //check coupon if exsits
        if(!empty($data['coupon']))
        {
            $code= $data['coupon'];
           // $coupon = Coupon::where('code',trim(strtolower($code)))->where('expires','>',time())->where('enabled',1)->first();
            $coupon = $cart->getCoupon($code);
            if(!$coupon){
                return jsonResponse([
                    'status'=>false,
                    'message'=>'Invalid Coupon Code'
                ]);
            }
            else{
                $cart->applyDiscount($code);
            }
        }

        $cart->setPaymentMethod($data['payment_method_id']);


        $invoice = Invoice::where('student_id',$studentId)->orderBy('invoice_id','desc')->first();
        if($invoice && $invoice->paid ==0){
            //create new invoice
            $invoice->cart = serialize($cart);
            $invoice->amount = price($cart->getCurrentTotal(),$currencyId,true);
            $invoice->payment_method_id = $data['payment_method_id'];
            $invoice->currency_id = $data['currency_id'];
            $invoice->save();
        }
        else{
            $invoice = Invoice::create([
                'student_id'=>$studentId,
                'currency_id'=>$data['currency_id'],
                'created_on'=>time(),
                'amount'=>price($cart->getCurrentTotal(),$currencyId,true),
                'cart' => serialize($cart),
                'paid'=> 0,
                'payment_method_id'=>$data['payment_method_id']
            ]);

        }
        //get payment method
        $paymentMethod = PaymentMethod::find($data['payment_method_id']);

        $sessions = [];
        foreach($cart->getSessions() as $session){
            $sessions[] = $session;
        }
//        $cart->setInvoice($invoice->invoice_id);
        $currencyObj = Currency::find($currencyId);
        $currency= $currencyObj->toArray();
        $currency['currency_symbol']=$currencyObj->country->symbol_left;

        return jsonResponse([
            'invoice'=>$invoice->toArray(),
            'payment_method'=>$paymentMethod->toArray(),
            'payment_required'=> $cart->requiresPayment(),
            'sessions'=> $sessions,
            'status'=>true,
            'has_discount'=>$cart->hasDiscount(),
            'discount_applied'=>$cart->getDiscount(),
            'discount_type'=>$cart->discountType(),
            'currency'=>$currency
        ]);

    }

    public function getInvoice(Request $request,Response $response,$args){
        $id = $args['id'];
        $student = $this->getApiStudent();
        $invoice = Invoice::find($id);
        if($invoice){
            if($invoice->student_id != $student->student_id){
                return jsonResponse([
                    'status'=>false
                ]);
            }
            $data = $invoice->toArray();
            return jsonResponse([
                'invoice'=>$data,
                'status'=>true
            ]);
        }
        else{
            return jsonResponse([
                'status'=>false
            ]);
        }
    }
}