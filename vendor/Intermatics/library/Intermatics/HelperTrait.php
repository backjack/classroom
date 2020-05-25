<?php
namespace Intermatics;
use Application\Entity\Country;
use Application\Entity\Invoice;
use Application\Entity\Setting;
use Application\Model\AccountsTable;
use Application\Model\CountryTable;
use Application\Model\SettingTable;
use Application\Model\StudentSessionTable;
use Application\Model\StudentTable;
use Aws\CloudFront\CloudFrontClient;
use Guzzle\Service\Resource\Model;
use Intermatics\Opencart\Library\Mail;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Uri\Http;
use Zend\View\Model\ViewModel;

/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 1/19/2017
 * Time: 1:18 PM
 */
trait HelperTrait
{

    private $data= [];
    private $validationError;
    public function canEnrollToSession($id){

        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        if(defined('STUDENT_LIMIT') && STUDENT_LIMIT > 0 &&  $studentSessionTable->getTotalActiveStudents() >= STUDENT_LIMIT ){

            $this->flashMessenger()->addMessage('Sorry! The maximum number of students has been reached');
            $this->notifyAdmins('Active Student Limit Reached','An enrollment was just attempted on your portal. However, you have reached the total limit of active students ('.STUDENT_LIMIT.') for your plan. Please upgrade your account allow more enrollments.');
            return false;
        }
        else{
            return true;
        }

    }

    public function canAddAdmin(){
        $accountsTable = new AccountsTable($this->getServiceLocator());
        if(defined('ADMIN_LIMIT') && ADMIN_LIMIT > 0 &&   $accountsTable->getTotal() >= ADMIN_LIMIT ){

            $this->flashMessenger()->addMessage('Sorry! The maximum number of admins/instructors has been reached.');
            return false;
        }
        else{
            return true;
        }

    }

    public function notifySessionStudents($sid,$subject,$message,$sms=true,$customSms=null){
        $sessionTable = new \Application\Model\SessionTable($this->getServiceLocator());
        $studentSessionTable = new \Application\Model\StudentSessionTable($this->getServiceLocator());

        $output = [];
        $count = 0;
        $totalRecords = $studentSessionTable->getTotalForSession($sid);
        $rowsPerPage = 3000;
        $totalPages = ceil($totalRecords/$rowsPerPage);

        $numbers = [];
        for($i=1;$i<=$totalPages;$i++){
            $paginator = $studentSessionTable->getSessionRecords(true,$sid,true);
            $paginator->setCurrentPageNumber($i);
            $paginator->setItemCountPerPage($rowsPerPage);

            foreach ($paginator as $row){

                $this->sendEmail($row->email,$subject,$message);
                if(!empty($row->mobile_number)){
                    $numbers[] = $row->mobile_number;
                }
                $count++;


            }
        }

        if($sms){
            $textMessage= strip_tags($message);
            if(!empty($customSms)){
                $textMessage = strip_tags($customSms);
            }
            $smsGateway = new SmsGateway($this->getServiceLocator(),$numbers,$textMessage);
            $smsGateway->send();
        }

    }

    public function sendEmail($recipientEmail, $title, $message, $sm = null,$senderName=null,$senderEmail=null)
    {
        if (empty($sm)) {
            $sm = $this->getServiceLocator();
        }
        $mode = getenv('APP_MODE');
        if(empty($senderEmail)){
            $senderEmail = $this->getSetting('general_admin_email', $sm);
        }


        if ($mode != 'demo' && !empty($senderEmail)) {
            $mailHandler = new Mail();
            if(empty($senderName)){
                $senderName = $this->getSetting('general_site_name', $sm,'TrainEasy');
            }

            $protocol = $this->getSetting('mail_protocol');
            if($protocol=='smtp'){
                $mailHandler->protocol = $protocol;
                $mailHandler->hostname = trim($this->getSetting('mail_smtp_host'));
                $mailHandler->username = trim($this->getSetting('mail_smtp_username'));
                $mailHandler->password = trim($this->getSetting('mail_smtp_password'));
                $port = $this->getSetting('mail_smtp_port');
                if(!empty($port)){
                    $mailHandler->port = trim($port);
                }

                $timeout = $this->getSetting('mail_smtp_timeout');
                if(!empty($timeout)){
                    $mailHandler->timeout = trim($timeout);
                }


            }

            $mailHandler->setFrom($senderEmail);
            $mailHandler->setSender($senderName);
            $mailHandler->setSubject($title);
            $mailHandler->setTo($recipientEmail);
            $mailHandler->setHtml($message);
            $mailHandler->send();
        }
    }



    public function setting($setting,$default){
        return $this->getSetting($setting,null,$default);
    }
    public function getSetting($setting, $sm = null,$default=null)
    {

        $row = Setting::where(['key'=>$setting])->first();
        if(empty($row->serialized)){
            $setting = $row->value;
        }
        else{
            $setting = unserialize($row->value);
        }

        if(!empty($setting)){
            return $setting;
        }
        elseif(!empty($default)){
            return $default;
        }
        else{
            return $setting;
        }
    }

    public function getBaseUrl()
    {
        $event = $this->getEvent();
        $request = $event->getRequest();
        $router = $event->getRouter();
        $uri = $router->getRequestUri();
        $baseUrl = sprintf('%s://%s%s', $uri->getScheme(), $uri->getHost(), $request->getBaseUrl());
        return $baseUrl;
    }

    public function studentIsLoggedIn(){

        $authService = $this->getAuthService();
        $role = UtilityFunctions::getRole();


        return ($authService->hasIdentity() && $role == 'student');
    }

    public function getStudent()
    {
        $authService = $this->getAuthService();
        $identity = $authService->getIdentity();
        $email = $identity['email'];
        $studentsTable = new StudentTable($this->getServiceLocator());
        $row = $studentsTable->tableGateway->select(array('email' => $email))->current();
        return $row;
    }

    public function getAdmin()
    {
        $authService = $this->getAuthService();
        $identity = $authService->getIdentity();
        $email = $identity['email'];
        $accountsTable= new \Application\Model\AccountsTable($this->getServiceLocator());
        $row = $accountsTable->getAccountWithEmail($email);
        return $row;
    }

    public function getAdminId(){
        $row= $this->getAdmin();
        return $row->account_id;
    }



    public function getId(){
        $row = $this->getStudent();
        return $row->student_id;
    }

    public function getAuthService()
    {

        return $this->getServiceLocator()->get('StudentAuthService');
    }

    public function deleteError(){
        $this->flashMessenger()->addMessage('Unable to delete because the record is locked!');
    }

    public function notifyAdmins($subject,$message,$sm=null){
        $sent = [];
        if(empty($sm))
        {
            $sm= $this->getServiceLocator();
        }
        $accountsTable= new \Application\Model\AccountsTable($sm);
        $rowset = $accountsTable->getAccountsForNotification();
        foreach ($rowset as $row) {
            if($accountsTable->hasPermission($row->account_id,'global_resource_access')){
                $this->sendEmail($row->email,$subject,$message);
                $sent[$row->email] = true;
            }

        }
        return $sent;
    }

    public function notifyStudent($id,$subject,$message){
        try{

            $accountsTable= new \Application\Model\StudentTable($this->getServiceLocator());
            $row = $accountsTable->getRecord($id);
            $this->sendEmail($row->email,$subject,$message);

        }
        catch(\Exception $ex)
        {

        }
    }

    public function notifyStudentSms($id,$message){

        $accountsTable= new \Application\Model\StudentTable($this->getServiceLocator());
        $student = $accountsTable->getRecord($id);

        $smsGateway = new SmsGateway($this->getServiceLocator(),$student->mobile_number,$message);
        return $smsGateway->send();

    }

    public function notifyAdmin($id,$subject,$message){
        try{

        $accountsTable= new \Application\Model\AccountsTable();
        $row = $accountsTable->getRecord($id);
        $this->sendEmail($row->email,$subject,$message);

            }
        catch(\Exception $ex)
        {

        }
    }

    public function sendEnrollMessage($student,$sessionName){
        $message = $student->first_name." ".$student->last_name.' just enrolled for a session: '.$sessionName;

        if($this->getSetting('regis_enrollment_alert')==1) {
            $this->notifyAdmins('New Enrollment', $message);
        }
    }

    public function validateOwner($row){
        if($row->student_id != $this->getId()){
            exit('You do not have permission to view this record');
        }
    }

    public function getApiStudentId(){
        return $this->getApiStudent()->student_id;
    }
    public function validateApiOwner($row){
        if($row->student_id != $this->getApiStudentId()){
            exit('You do not have permission to view this record');
        }
    }

    public function getCurrencyCode()
    {


        $code = currentCurrency()->country->currency_code;

        return $code;
    }

    public function logPayment($invoiceId){
        try{


        $invoice= Invoice::find($invoiceId);
        if(!$invoice){
            return false;
        }
        $amount  = $invoice->amount;
        $studentId = $invoice->student_id;
        $paymentMethodId = $invoice->payment_method_id;
        $currency = $invoice->currency;

        $defaultCountry = Country::find($this->getSetting('country_id'));


        if($defaultCountry->currency_code != $currency->country->currency_code){
            $rate = $currency->exchange_rate;
            $amount = $amount / $rate;
        }

        $data = [
            'amount'=>$amount,
            'student_id'=>$studentId,
            'added_on'=>time(),
            'payment_method_id'=>$paymentMethodId
        ];

        $table = new \Application\Model\PaymentTable($this->getServiceLocator());
        $id = $table->addRecord($data);
        return $id;
        }
        catch(\Exception $ex){
            return false;
        }
    }

    public function addPayment($amount,$studentId,$paymentMethodId){


        $data = [
          'amount'=>$amount,
            'student_id'=>$studentId,
            'added_on'=>time(),
            'payment_method_id'=>$paymentMethodId
        ];

        $table = new \Application\Model\PaymentTable($this->getServiceLocator());
        $id = $table->addRecord($data);
        return $id;
    }

    public function goBack()
    {

        if(isset($_SERVER['HTTP_REFERER']) )
        {
            $oLink = $_SERVER['HTTP_REFERER'];

            if($oLink == selfURL()){
                return $this->redirect()->toRoute('home');
            }

            if(substr_count($_SERVER['HTTP_REFERER'],'redirect')>0)
            {
                $link = $_SERVER['HTTP_REFERER'];
            }
            elseif(substr_count($_SERVER['HTTP_REFERER'],'?')>0){
                $link = $_SERVER['HTTP_REFERER'].'&redirect=true';
            }
            else{
                $link = $_SERVER['HTTP_REFERER'].'?redirect=true';
            }

                $this->redirect()->toUrl($link);



        }
        else{
            $this->redirect()->toRoute('home');
        }

    }

    public function getFormErrors($form){
        $errors = $form->getMessages();
        $message= __('submission-failed-attend');
        foreach($errors as $key=>$value){
            $label = $form->get($key)->getLabel();
            if(!empty($label)){
                $message .= '<strong>'.ucfirst($form->get($key)->getLabel()).'</strong>: ';
            }

            foreach($value as $msg){
                $message .= $msg.'. ';
            }
            $message .= '<br/>';
        }
        return $message;
    }

    public function hasPermission($path){
        $permissionTable = new \Application\Model\PermissionTable($this->getServiceLocator());
        return $permissionTable->hasPermission($path);
    }

    public function getViewModel($data){
        $viewModel = new ViewModel($data);
        $action = $this->getEvent()->getRouteMatch()->getParam('action', 'index');
        $controllerName = $this->getEvent()->getRouteMatch()->getParam('controller', 'index');

        $cname ='\\'.ucfirst($this->getEvent()->getRouteMatch()->getParam('__CONTROLLER__', 'index'));
        //$dcname = str_replace($cname, '', $controllerName);


        //get position of last dash
        $pos = strrpos($controllerName, '\\');
        $directory= substr($controllerName,0, $pos);

        $pos = strrpos($directory, '\\');
        $directory= strtolower(substr($directory,$pos+1));

        $controllerName = strtolower(substr($controllerName,strrpos($controllerName, '\\')));
        $controllerName = str_ireplace('\\','',$controllerName);

        $extension = '.phtml';
        $e  = $this->getEvent();
        $controller      = $e->getTarget();
        $controllerClass = get_class($controller);
        $module = substr($controllerClass, 0, strpos($controllerClass, '\\'));

        $fileName = "module/$module/view/templates/".TID."/$controllerName/$action".$extension;

        if(file_exists($fileName))
        {

            $viewModel->setTemplate('templates/'.TID.'/'.$controllerName.'/'.$action);
        }
        return $viewModel;
    }

    public function absoluteRoute($route,$params=[]){
       $url= $this->url()->fromRoute($route,$params, array('force_canonical' => true));

        if($this->getSetting('general_ssl')==1){
            $url = str_ireplace('http://','https://',$url);

        }

        return $url;
    }

    public function isValid($data,$rules){


        $form = new Form();
        foreach($data as $key=>$value){
            $form->add(new Element($key));
        }

        //create input filter
        $inputFilter = new InputFilter();
        foreach($rules as $key=>$data){



        }

    }

    public function saveInlineImages($html,$basePath){
        $savePath = USER_PATH.'/user_editor_images/'.date('m_Y');
        $saveUrl = $basePath.'/'.str_replace('public/','',$savePath);
        if(!file_exists($savePath)){
            mkdir($savePath,0777, true);
        }
        $dom = new \DOMDocument();

        @$dom->loadHTML('<?xml encoding="utf-8" ?>' .$html);
        foreach($dom->getElementsByTagName('img') as $element){
            //This selects all elements
            $data = $element->getAttribute('src');



            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif

                if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                    throw new \Exception('invalid image type');
                }

                $data = base64_decode($data);

                if ($data === false) {
                    continue;
                }

                $fileName = time().rand(100,10000);
                file_put_contents($savePath."/{$fileName}.{$type}", $data);
                $element->setAttribute('src',$saveUrl.'/'.$fileName.'.'.$type);

            } else {
                continue;
            }



        }

        $body = "";
        foreach($dom->getElementsByTagName("body")->item(0)->childNodes as $child) {
            $body .= $dom->saveHTML($child);
        }

        return $body;


    }

    public function getS3Client(){
        $config = $this->getServiceManager()->get('config');
        $s3Config = $config['s3'];

        $credentials = new \Aws\Credentials\Credentials($s3Config['key'], $s3Config['secret']);

        $s3Client = new \Aws\S3\S3Client([
            'version' => '2006-03-01',
            'region'  => $s3Config['region'],
            'credentials'=> $credentials
        ]);

        //check if user has bucket, create if not

        return $s3Client;
    }

    public function getCloudFrontClient(){
        $config = $this->getServiceManager()->get('config');
        $s3Config = $config['s3'];

        $credentials = new \Aws\Credentials\Credentials($s3Config['key'], $s3Config['secret']);

        $client = new CloudFrontClient([
            'version' => '2014-11-06',
            'region'  => $s3Config['region']
        ]);

        return $client;
    }


    public function getS3BucketName(){
        $config = $this->getServiceManager()->get('config');
        $s3Config = $config['s3'];
        $bucket = $s3Config['bucket'];
        return $bucket;
    }

    public function validate($data,$rules){
       $result= \GUMP::is_valid($data,$rules);
        if($result === true){
            return true;
        }
        else{
            $this->validationError = $result;
            return false;
        }
    }

    public function getValidationErrors(){
        $errors = $this->validationError;
        foreach($errors as $key=>$value){
            $errors[$key] = strip_tags($value);
        }

        return $errors;
    }

    public function getValidationErrorMsg(){
        $errors = $this->validationError;
        foreach($errors as $key=>$value){
            $errors[$key] = strip_tags($value);
        }
        $errorString = implode(' , ',$errors);

        return $errorString;
    }


    public function mailSurvey($surveyId,$studentId){
        $survey = \Application\Entity\Survey::find($surveyId);

        if($survey){
            $link = $this->absoluteRoute('survey',['hash'=>$survey->hash]);
            $subject= __('survey').': '.$survey->name;
            $message =  $survey->description.' <br/>'.__('survey-mail').": <a href=\"{$link}\">{$link}</a>";
            $this->notifyStudent($studentId,$subject,$message);
        }

    }
}