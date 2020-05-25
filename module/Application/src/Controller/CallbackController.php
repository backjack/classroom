<?php


namespace Application\Controller;


use Application\Entity\Invoice;
use Application\Entity\InvoiceTransaction;
use Application\Entity\Transaction;
use Application\Model\InvoiceInvoiceTransactionTable;
use Application\Model\PaymentMethodFieldTable;
use Application\Model\PaymentTable;
use Application\Model\SessionTable;
use Application\Model\StudentSessionTable;
use Application\Model\InvoiceTransactionTable;
use Intermatics\HelperTrait;
use Omnipay\Common\CreditCard;
use Omnipay\Omnipay;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Stripe;
use Unirest\Request\Body;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;

class CallbackController extends AbstractController {
    use HelperTrait;
    private $data = [];

    /**
     * @return \Zend\Http\Response
     * The callback for paystack payment method
     */
    public function paystackAction()
    {
        $this->loadValues('paystack');
        $sessionTable = new SessionTable($this->getServiceLocator());
        $transactionTable = new InvoiceTransactionTable($this->getServiceLocator());
         
         
        $tid = $this->request->getPost('paystack-trxref');

        //check if transaction is successful


        try{
            if(!$transactionTable->transactionExists($tid))
            {
                throw new \Exception(__('Invalid Transaction'));
            }

            $trow = $transactionTable->getRecord($tid);
            if($trow->status=='s'){
                return $this->redirect()->toRoute('home');
            }

            $authorization = "Authorization: Bearer ".$this->data['secret_key'];
            $endpoint = 'https://api.paystack.co/transaction/verify/'.$tid;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $endpoint);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            $result = curl_exec($ch);

            curl_close($ch);
            $obj = json_decode($result);



            $statusCode = $obj->status;

            $statusMessage = $obj->message;
            if($statusCode==1){
                $responseAmount  = $obj->data->amount/100;
            }
            else{
                $responseAmount  = 0;
            }

            if($responseAmount  != floatval($trow->amount)) {
                  throw new \Exception(__('approved-amount error',['statusCode'=>$statusCode,'respAmount'=>$obj->data->amount,'transAmount'=>$trow->amount]));
            }

            if ($obj->status != 1)
            {
                throw new \Exception(__('Payment failed!'));
            }
            else{
                $transactionTable->update(['status'=>'s'],$tid);
               return $this->approve(4);
            }

        }
        catch(\Exception $ex)
        {
            $this->flashMessenger()->addMessage(__('payment-unsuccessful').$ex->getMessage());
            $this->redirect()->toRoute('cart');
           // throw new \Exception('Error validating transaction. Please try again.');
        }


    }

    public function raveAction(){
        $this->loadValues('rave');
        $response = $_REQUEST['resp'];
        $responsObj= json_decode($response);

        $ref = $responsObj->data->tx->txRef;



        $data = array('txref' => $ref,
            'SECKEY' => $this->data['skey'], //secret key from pay button generated on rave dashboard
            'include_payment_entity' => 1
        );




        if($this->data['mode']==0){
            $endPoint = 'https://ravesandboxapi.flutterwave.com/flwv3-pug/getpaidx/api/v2/verify';
        }
        else{
            $endPoint = 'https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify';
        }


        // make request to endpoint using unirest.
        $headers = array('Content-Type' => 'application/json');
        $body = Body::json($data);


        try{

            $response = \Unirest\Request::post($endPoint, $headers, $body);

            $transaction = InvoiceTransaction::findOrFail($ref);

            //check the status is success
            if ($response->body->status === "success" && $response->body->data->chargecode === "00") {



                //confirm that the amount is the amount you wanted to charge
                if (floatval($response->body->data->amount) === floatval($transaction->amount)) {

                    $transaction->status = 's';
                    $transaction->save();
                  return  $this->approve(10);
                }
                else{

                    throw new \Exception(__('Invalid amount received'));
                }
            }
            else{

                throw new \Exception(__('Payment failed!'));

            }


        }
        catch(\Exception $ex){
            $this->flashMessenger()->addMessage(__('payment-unsuccessful').$ex->getMessage());
            return $this->redirect()->toRoute('cart');
        }

    }

    /**
     * The callback for the stripe payment
     * method
     */
    public function stripeAction()
    {
    /*    $session = new Container('enroll');
        $sessionTable = new SessionTable($this->getServiceLocator());
        $id = $session->id;
        $sessionRow = $sessionTable->getRecord($id);*/
        if(!$this->request->isPost())
        {
            $this->redirect()->toRoute('cart');
        }
        $this->loadValues('stripe');
        $token  = $this->request->getPost('stripeToken');
        $student = $this->getStudent();
        Stripe::setApiKey($this->data['skey']);

        try{

             $customer = Customer::create([
                   'email'=>$student->email,
                    'source'=>$token
                ]);

            $charge = Charge::create(array(
                'customer' => $customer->id,
                'amount'   => (getCart()->getInvoiceObject()->amount * 100),
                'currency' => strtolower($this->getCurrencyCode())
            ));

           return $this->approve(5);

            }
           catch(\Exception $ex){


               $this->flashMessenger()->addMessage(__('payment-unsuccessful').$ex->getMessage());
               $this->redirect()->toRoute('cart');
            }

    }

    /**
     * @return \Zend\Http\Response
     * The callback for paypal payment method
     */
    public function paypalAction()
    {

        $session = new Container('paypal');
        $this->loadValues('paypal');
        $gateway = Omnipay::create('PayPal_Rest');
        $gateway->initialize(array(
        'clientId' => trim($this->data['clientid']),
        'secret'   => trim($this->data['secret']),
        'testMode' => empty($this->data['mode']), // Or false when you are ready for live transactions

      ));

        try {

            $transaction = $gateway->completePurchase(array(
                'payerId'             => $_REQUEST['PayerID'],
                'transactionReference' => $session->transactionRef
            ));

            $finalResponse = $transaction->send();



            if ($finalResponse->isSuccessful()) {

                // Find the authorization ID
                $results = $finalResponse->getTransactionReference();
                return $this->approve(2);

            }else{
                throw new \Exception(__('Transaction failed!'));
            }



    } catch (\Exception $e) {
            $this->flashMessenger()->addMessage(__('payment-unsuccessful').$e->getMessage());
            $this->redirect()->toRoute('cart');

               }

      return  $this->redirect()->toRoute('cart');
        
    }

    /**
     * The callback for 2checkout payment method.
     */
    public function twocheckoutAction(){

        $this->loadValues('twocheckout');
        $hashSecretWord = trim($this->data['secretWord']); //2Checkout Secret Word
        $hashSid = trim($this->data['accountNumber']); //2Checkout account number
        $hashTotal = number_format(floatval(getCart()->getInvoiceObject()->amount), 2, '.', ''); //Sale total to validate against
        $hashOrder = $_REQUEST['order_number']; //2Checkout Order Number
        $StringToHash = strtoupper(md5($hashSecretWord . $hashSid . $hashOrder . $hashTotal));
        if ($StringToHash != $_REQUEST['key'] || $_REQUEST['credit_card_processed'] != 'Y' ) {
            $result = 'Payment Failed. Kindly verify your card details.';
            $append = 'Failed';
            $this->flashMessenger()->addMessage(__('payment-unsuccessful'));
            $this->redirect()->toRoute('cart');

        } else {
            $result = __('payment-success');
           return $this->approve(3);

        }
    }


    public function payuAction(){

        $this->loadValues('payu');
        $transactionState = 'failure';
        try {
            $message = '';

            if(!empty($_GET['PayUReference'])) {
                //Creating get transaction soap data array
                $getTransactionData = array();
                $getTransactionData['AdditionalInformation']['payUReference'] = $_GET['PayUReference'];
                $config = array();
                $config['safe_key'] = $this->data['payu_easyplus_safe_key'];
                $config['api_username'] = $this->data['payu_easyplus_api_username'];
                $config['api_password'] = $this->data['payu_easyplus_api_password'];
                $config['logEnable'] = $this->data['payu_easyplus_api_password'];
                $config['extended_debug'] = $this->data['payu_easyplus_api_password'];
                if($this->data['payu_easyplus_transaction_mode'] == 'production') {
                    $config['production'] = true;
                }
                require_once 'vendor/payu/classes/PayUEasyPlus.php';
                $payUEasyPlus = new \PayUEasyPlus($config);
                $response = $payUEasyPlus->doGetTransaction($getTransactionData);

                //var_dump();
                //exit;
                $message = $response['soap_response']['displayMessage'];

                //Checking the response from the SOAP call to see if successfull
                if(isset($response['soap_response']['successful'])
                    && $response['soap_response']['successful'])
                {
                    if(isset($response['soap_response']['transactionType'])
                        && $response['soap_response']['transactionType'] == $this->data['payu_easyplus_transaction_type'])
                    {
                        $MerchantReferenceCheck = $this->session->data['order_id'];
                        $invoiceId = $this->data['invoice']->invoice_id;
                        $MerchantReferenceCallBack = $response['soap_response']['merchantReference'];

                        $transaction= InvoiceTransaction::find($MerchantReferenceCallBack);
                        if($transaction->invoice_id==$invoiceId){
                            $gatewayReference = $response['soap_response']['paymentMethodsUsed']['gatewayReference'];
                            $transactionState = 'paymentSuccessfull';
                        }
                        else{
                            $message = __('Invalid payment');
                        }

                    }
                } else {
                    $message = $response['soap_response']['displayMessage'];
                }
            }
        } catch(\Exception $e) {
            $message = $e->getMessage();
        }

        //Now doing db updates for the orders
        if($transactionState == 'paymentSuccessfull')
        {
            $message = '---Payment Successful---'."\r\n";
            $message .= 'Order ID: ' . $this->session->data['order_id'] . "\r\n";
            $message .= 'PayU Reference: ' . $this->request->get['PayUReference'] . "\r\n";
            foreach ($response['soap_response']['paymentMethodsUsed'] as $key => $value) {
                $message .= ucwords($key) . ': ' . $value . "\r\n";
            }

            $this->flashMessenger()->addMessage($message);
           return $this->approve(6);



        } else if($transactionState == "failure") {

            $message = __("payment-failed-reason") . $message;
            $this->flashMessenger()->addMessage($message);
            return $this->redirect()->toRoute('cart');
        }


    }


    public function payuipnAction(){
        try{


        $this->loadValues('payu');
        $ipnData = file_get_contents('php://input');
        $xml = @simplexml_load_string($ipnData);

        if(false === $xml)
        {
            $this->notifyAdmins('False xml',$ipnData);
                exit('False xml');
        }

        $ipn = $this->parseXMLToArray($xml);

        if(false === $ipn)
        {
            $this->notifyAdmins('False ipn',$ipn);
            exit('False ipn');
        }


        if (!isset($ipn['MerchantReference'])) {
            $this->notifyAdmins('False merchant reference',$ipnData);
            exit('False merchant reference');
        }

        $orderid = $ipn['MerchantReference'];

       // $order_info = $this->model_checkout_order->getOrder($orderid);
        $transaction = InvoiceTransaction::find($orderid);

        $payUReference = intval($ipn['PayUReference']);
        $txn_type = $ipn['TransactionType'];
        $payment_amount = (float)$ipn['PaymentMethodsUsed']['AmountInCents'] / 100;
        $payment_currency = $ipn['Basket']['CurrencyCode'];
        $payment_status = $ipn['TransactionState'];
        $hash = $ipn['IpnExtraInfo']['ResponseHash'];

        if($transaction) {
            $order_id = $transaction->invoice_transaction_id;
            $ipnNote = '-----PAYU IPN RECIEVED---' . "\r\n";
            $ipnNote .= 'PayU Reference: ' . $payUReference . "\r\n";
            switch ($payment_status) {
                case 'SUCCESSFUL':
                    if (abs($payment_amount - $transaction->amount) > 0.01) {
                        $ipnNote .= 'Payment did not equal the order total. ';
                        exit();
                    }
                    $this->approveTransaction($order_id);



                    break;

                case 'EXPIRED':
                    $transaction->status = 'f';
                    $transaction->save();
                    break;

                case 'FAILED':
                      $transaction->status = 'f';
                    $transaction->save();
                    break;

                case 'AWAITING_PAYMENT':
                    //$this->model_checkout_order->update($order_id, 1, $ipnNote . 'Awating Payment confirmation for EFT PRO at PayU: ' . $ipnData['resultMessage']);
                    break;

                case 'PROCESSING':
                    //$this->model_checkout_order->update($order_id, 2, $ipnNote . 'A payment has been created but not finalized.');
                    break;

                case 'TIMEOUT':
                     $transaction->status = 'f';
                    $transaction->save();
                    break;
            }
        } else {
            $transaction->status = 'f';
            $transaction->save();
         }

        header("HTTP/1.1 200 Ok");
        exit();

        }
        catch(\Exception $ex){
            $this->notifyAdmins('Payu ipn error',$ex->getMessage().'<br/>'.$ex->getTraceAsString());
            mail('ayokunle@traineasy.net','Payu ipn error',$ex->getMessage().'<br/>'.$ex->getTraceAsString());
            exit();
        }
    }

    public function payfastAction(){
        unset($_SESSION['enroll']);
        $this->flashMessenger()->addMessage(__('Transaction Complete!'));
        return $this->redirect()->toRoute('application/dashboard');
    }

    public function payfastitnAction() {

        try{


        $id = $this->params('id');

        $this->loadValues('payfast');

        $pfHost = ($this->data['payfast_sandbox'] ? 'sandbox' : 'www') . '.payfast.co.za';
        if($this->data['payfast_debug']){$debug = true;}else{$debug = false;}
        define( 'PF_DEBUG', $debug );

        require_once 'vendor/payfast/payfast_common.inc';
        $pfError = false;
        $pfErrMsg = '';
        $pfDone = false;
        $pfData = array();
        $pfParamString = '';
        if (isset($_POST['custom_str1']))
        {
            $order_id = $_POST['custom_str1'];
        } else {
            $order_id = 0;
        }


        pflog( 'PayFast ITN call received' );

        //// Notify PayFast that information has been received
        if( !$pfError && !$pfDone )
        {
            header( 'HTTP/1.0 200 OK' );
            flush();
        }

        //// Get data sent by PayFast
        if( !$pfError && !$pfDone )
        {
            pflog( 'Get posted data' );

            // Posted variables from ITN
            $pfData = pfGetData();
            $pfData['item_name'] = html_entity_decode( $pfData['item_name'] );
            $pfData['item_description'] = html_entity_decode( $pfData['item_description'] );
            pflog( 'PayFast Data: '. print_r( $pfData, true ) );

            if( $pfData === false )
            {
                $pfError = true;
                $pfErrMsg = PF_ERR_BAD_ACCESS;
            }
        }

        //// Verify security signature
        if( !$pfError && !$pfDone )
        {
            pflog( 'Verify security signature' );
            $passphrase = $this->data['payfast_passphrase'];
            $pfPassphrase = $this->data['payfast_sandbox'] ? null : ( !empty( $passphrase ) ? $passphrase : null );
            // If signature different, log for debugging
            if( !pfValidSignature( $pfData, $pfParamString, $pfPassphrase ) )
            {
                $pfError = true;
                $pfErrMsg = PF_ERR_INVALID_SIGNATURE;
            }
        }

        //// Verify source IP (If not in debug mode)
        if( !$pfError && !$pfDone && !PF_DEBUG )
        {
            pflog( 'Verify source IP' );

            if( !pfValidIP( $_SERVER['REMOTE_ADDR'] ) )
            {
                $pfError = true;
                $pfErrMsg = PF_ERR_BAD_SOURCE_IP;
            }
        }
        //// Get internal cart
        if( !$pfError && !$pfDone )
        {
            // Get order data

            $order_info = InvoiceTransaction::find($id);

            pflog( "Purchase:\n".$order_info->invoice_transaction_id   );
        }

        //// Verify data received
        if( !$pfError )
        {
            pflog( 'Verify data received' );

            $pfValid = pfValidData( $pfHost, $pfParamString );

            if( !$pfValid )
            {
                $pfError = true;
                $pfErrMsg = PF_ERR_BAD_ACCESS;
            }
        }

        //// Check data against internal order
        if( !$pfError && !$pfDone )
        {
            pflog( 'Check data against internal order' );

            $amount = $order_info->amount;
            // Check order amount
            if( !pfAmountsEqual( $pfData['amount_gross'],$amount ) )
            {
                $pfError = true;
                $pfErrMsg = PF_ERR_AMOUNT_MISMATCH;
            }

        }

        //// Check status and update order
        if( !$pfError && !$pfDone )
        {
            pflog( 'Check status and update order' );


            $transaction_id = $pfData['pf_payment_id'];

            switch( $pfData['payment_status'] )
            {
                case 'COMPLETE':
                    pflog( '- Complete' );

                    // Update the purchase status
                    $this->approveTransaction($id);
                    break;

                case 'FAILED':
                    pflog( '- Failed' );

                    // If payment fails, delete the purchase log
                    $message = "Payment failed. Please try again.";
                    break;

                case 'PENDING':
                    pflog( '- Pending' );
                    $message = "Payment pending.";
                    // Need to wait for "Completed" before processing
                    break;

                default:
                    // If unknown status, do nothing (safest course of action)
                    $message = "Payment failed. Please try again.";
                    break;
            }

        }
        else
        {
            pflog( "Errors:\n". print_r( $pfErrMsg, true ) );
            $message = "Payment failed. ". print_r( $pfErrMsg, true );
            $this->flashMessenger()->addMessage($message);
            return $this->redirect()->toRoute('cart');
        }

        exit();
        }
        catch(\Exception $ex){
            pflog($ex->getMessage().' | '.$ex->getTraceAsString());
            exit();
        }
    }


    public function payumoneyAction()
    {
        if(!$this->request->isPost()){
            exit('Invalid request');
        }

        $this->loadValues('payumoney');
        $sessionTable = new SessionTable($this->getServiceLocator());
        $transactionTable = new InvoiceTransactionTable($this->getServiceLocator());
//        $session = new Container('enroll');
        $id = getCart()->getInvoice();
        $tid = $_POST["txnid"];

        $status=$_POST["status"];
        $firstname=$_POST["firstname"];
        $amount=$_POST["amount"];
        $txnid=$_POST["txnid"];
        $posted_hash=$_POST["hash"];
        $key=$_POST["key"];
        $productInfo=$_POST["productinfo"];
        $email=$_POST["email"];
        $salt=$this->data['payumoney_salt'];
        $udf5 		  = "traineasy";


        //check if transaction is successful
        If (isset($_POST["additionalCharges"])) {
            $additionalCharges=$_POST["additionalCharges"];
            $retHashSeq = $additionalCharges.'|'.$salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productInfo.'|'.$amount.'|'.$txnid.'|'.$key;
        } else {
            $retHashSeq = $salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productInfo.'|'.$amount.'|'.$txnid.'|'.$key;
        }

        $hash = hash("sha512", $retHashSeq);

        try{
            if(!$transactionTable->transactionExists($tid))
            {
                throw new \Exception('Invalid Transaction');
            }

            $trow = $transactionTable->getRecord($tid);
            if($trow->status=='s'){
                return $this->redirect()->toRoute('home');
            }



            if($amount  != floatval($trow->amount)) {
                throw new \Exception(__("invalid-amount-rec"));
            }

            if ($hash != $posted_hash)
            {
                throw new \Exception(__('Payment failed!'));
            }
            else{
                $transactionTable->update(['status'=>'s'],$tid);
                return $this->approve(8);
            }

        }
        catch(\Exception $ex)
        {
            $this->flashMessenger()->addMessage(__('payment-unsuccessful').$ex->getMessage());
            $this->redirect()->toRoute('cart');
            // throw new \Exception('Error validating transaction. Please try again.');
        }


    }


    public function ipayAction()
    {
        unset($_SESSION['enroll']);
        $this->flashMessenger()->addMessage(__('Transaction Complete!'));
        return $this->redirect()->toRoute('application/dashboard');

    }

    public function ipayipnAction(){
        $this->loadValues('ipay');
        $tid = trim($_GET['invoice_id']);
        $transaction = InvoiceTransaction::findOrFail($tid);
        $merchantKey = trim($this->data['ipay_merchant_key']);

        $url = 'https://community.ipaygh.com/v1/gateway/status_chk?invoice_id='.$tid.'&merchant_key='.$merchantKey;
        $status = file_get_contents($url);
        $statusArray = explode('~',$status);

        if(trim($statusArray[1])=='paid'){
            $this->approveTransaction($tid);
        }
        exit('');

    }

    /**
     * @param $code
     * @throws \Exception
     * This function loads the credentails of a payment method
     * from the database into the $data property of this class.
     */
    private function loadValues($code)
    {

        $cart = getCart();
        $id = $cart->getInvoice();
        $paymentFieldsTable = new PaymentMethodFieldTable($this->getServiceLocator());
        $rowset = $paymentFieldsTable->getCodeValues($code);

        foreach ($rowset as $row) {
            $this->data[$row->key] = $row->value;
        }

        if(!empty($id)){
            $this->data['invoice'] = $cart->getInvoiceObject();
        }

    }



    /**
     * @param $methodId
     * @return \Zend\Http\Response
     * @throws \Exception
     * This method should be called when a payment has been confirmed. It approves a payment
     * and enrolls the student to the selected session.
     */
    private function approve($methodId){

        $paymentTable = new PaymentTable($this->getServiceLocator());
//        $session = new Container('enroll');
        $cart = getCart();
        $id = $cart->getInvoice();
        if(empty($id)){
            return $this->goBack();
        }


        //log payment
        //$this->addPayment($cart->getInvoiceObject()->amount,$this->getId(),$methodId);
        $this->logPayment($id);

        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $code = generateRandomString(5);
        $total = $cart->approve($this->getId());
        $message = __('enroll-success-msg',['total'=>$total]);
        $this->flashMessenger()->addMessage($message);
        return  $this->redirect()->toRoute('application/default',['controller'=>'student','action'=>'mysessions']);


        /*   $studentSessionTable->addRecord(array(
               'student_id'=>$this->getId(),
               'session_id'=>$id,
               'reg_code'=>$code,
               'enrolled_on'=>time()
           ));

           $student = $this->getStudent();
           $message = "<h4>Your enrollment code is $code</h4>";
           $emailMessage = $message.$this->getSetting('regis_email_message',$this->getServiceLocator());
           $this->sendEmail($student->email,'Enrollment Complete',$emailMessage,$this->getServiceLocator());
           $this->sendEnrollMessage($student,$row->session_name);

           $type = ($row->session_type=='c')? 'Course':'Session';
           $sessionName =$row->session_name;
           $message = "You have successfully enrolled for the $sessionName $type!";
           $this->flashMessenger()->addMessage($message);
           unset($_SESSION['enroll']);

           if($row->session_type!='c'){
              return $this->redirect()->toRoute('session-details',array('id'=>$row->session_id));
           }
           else{
               //redirect to the course introduction page
             return  $this->redirect()->toRoute('application/default',['controller'=>'course','action'=>'intro','id'=>$row->session_id]);
           }
           */
    }

    private function approveTransaction($transactionId){
        $transaction = InvoiceTransaction::find($transactionId);
        $transaction->status = 's';
        $transaction->save();

     //   $this->addPayment($transaction->amount,$transaction->invoice->student_id,$transaction->invoice->payment_method_id);

        $this->logPayment($transaction->invoice_id);
        $cart = unserialize($transaction->invoice->cart);
        $total = $cart->approve($transaction->invoice->student_id);
        /*
         * $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $code = generateRandomString(5);
        $studentSessionTable->addRecord(array(
            'student_id'=>$transaction->student_id,
            'session_id'=>$transaction->session_id,
            'reg_code'=>$code,
            'enrolled_on'=>time()
        ));

        $student = $transaction->student;
        $message = "<h4>Your enrollment code is $code</h4>";
        $emailMessage = $message.$this->getSetting('regis_email_message',$this->getServiceLocator());
        $this->sendEmail($student->email,'Enrollment Complete',$emailMessage,$this->getServiceLocator());
        $this->sendEnrollMessage($student,$transaction->session->session_name);

        */
    }

    private function parseXMLToArray($xml)
    {
        if($xml->count() <= 0)
            return false;

        $data = array();
        foreach ($xml as $element) {
            if($element->children()) {
                foreach ($element as $child) {
                    if($child->attributes()) {
                        foreach ($child->attributes() as $key => $value) {
                            $data[$element->getName()][$child->getName()][$key] = $value->__toString();
                        }
                    } else {
                        $data[$element->getName()][$child->getName()] = $child->__toString();
                    }
                }
            } else {
                $data[$element->getName()] = $element->__toString();
            }
        }
        return $data;
    }

}