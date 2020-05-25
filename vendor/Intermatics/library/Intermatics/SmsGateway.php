<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 3/23/2018
 * Time: 11:34 AM
 */

namespace Intermatics;


use Application\Model\SettingTable;
use Application\Model\SmsGatewayFieldTable;
use Application\Model\SmsGatewayTable;

class SmsGateway {

    private $serviceLocator;
    private $recipients;
    private $message;
    private $senderName;
    private $smsEnabled;
    private $smsGatewayFieldTable;

    public function __construct($serviceLocator,$recipients,$message){
        $this->serviceLocator = $serviceLocator;
        $this->recipients = $recipients;
        $this->message = $message;
        $settingTable = new SettingTable($serviceLocator);
        $this->senderName= $settingTable->getSetting('sms_sender_name');
        $this->smsEnabled= $settingTable->getSetting('sms_enabled')==1?true:false;
        $this->smsGatewayFieldTable = new SmsGatewayFieldTable($serviceLocator);
    }



    public function send(){

        $smsGatewayTable = new SmsGatewayTable($this->serviceLocator);
        $activeGateway= $smsGatewayTable->getActiveGateway();
        if(!$activeGateway){
            return 'Sending failed. There is no currently installed sms gateway';
        }

        if(!$this->smsEnabled){
            return 'Sending failed. SMS is disabled';
        }


        if(empty($this->recipients)){
            return 'Sending failed. There are no recipients to this message';
        }

        $code= $activeGateway->code;
        try{
            return call_user_func([$this,$code]);
        }
        catch(\Exception $ex){
            return $ex->getMessage();
        }


    }


    public function smartsms(){


        $gatewayid=1;
        $apiGetEndpoint = 'http://api.smartsmssolutions.com/smsapi.php';
        $smsUsername = $this->smsGatewayFieldTable->getField($gatewayid,'username');
        $smsPassword  = $this->smsGatewayFieldTable->getField($gatewayid,'password');

        $pendingNumberlist = '';
        if(is_array($this->recipients)){
            foreach($this->recipients as $value){
                $pendingNumberlist .= $value.',';
            }

        }
        else{
            $pendingNumberlist = $this->recipients;
        }
        $smsArray = [
            'username'=>$smsUsername,
            'password'=>$smsPassword,
            'message'=>$this->message,
            'sender'=>$this->senderName,
            'recipient'=>$pendingNumberlist
        ];

        $data = $this->sendsms_post($apiGetEndpoint,$smsArray);
        return $data;
    }


    public function cheapglobal(){
        $gatewayid=2;


        $pendingNumberlist = '';
        if(is_array($this->recipients)){
            foreach($this->recipients as $value){
                $pendingNumberlist .= $value.',';
            }

        }
        else{
            $pendingNumberlist = $this->recipients;
        }

        $post_data=array(
            'sub_account'=>$this->smsGatewayFieldTable->getField($gatewayid,'sub_account'),
            'sub_account_pass'=>$this->smsGatewayFieldTable->getField($gatewayid,'sub_account_pass'),
            'action'=>'send_sms',
            'sender_id'=>$this->senderName,
            'recipients'=>$pendingNumberlist,
            'message'=>$this->message
        );

        $api_url='http://cheapglobalsms.com/api_v1';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if($response_code != 200)$response=curl_error($ch);
        curl_close($ch);

        if($response_code != 200)$msg="HTTP ERROR $response_code: $response";
        else
        {
            $json=@json_decode($response,true);

            if($json===null)$msg="INVALID RESPONSE: $response";
            elseif(!empty($json['error']))$msg=$json['error'];
            else
            {
                $msg="SMS sent to ".$json['total']." recipient(s).";
                $sms_batch_id=$json['batch_id'];
            }
        }

        return $msg;

    }



    public function clickatell(){
        $gatewayid=3;

        $apiKey= $this->smsGatewayFieldTable->getField($gatewayid,'apikey');
        $numbers = [];

        if(is_array($this->recipients)){
            $numbers = $this->recipients;
        }
        else{
            $numbers[] = $this->recipients;
        }
        $count = 0;
        foreach($numbers as $key=>$value){
            $number = str_ireplace('+','',$value);
            $url =  'https://platform.clickatell.com/messages/http/send?apiKey='.urlencode($apiKey).'&to='.$number.'&content='.urlencode($this->message);
            file_get_contents($url);
            $count++;
        }

        return 'Message sent to '.$count.' recipients';

    }









//--------------------Helpers---------------------------//
    private function sendsms_post ($url, array $params) {     $params = http_build_query($params);     $ch = curl_init();           curl_setopt($ch, CURLOPT_URL,$url);     curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);     curl_setopt($ch, CURLOPT_POST, 1);     curl_setopt($ch, CURLOPT_POSTFIELDS, $params);          $output=curl_exec($ch);       curl_close($ch);     return $output;         }

}