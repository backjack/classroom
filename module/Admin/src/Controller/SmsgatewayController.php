<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 3/15/2018
 * Time: 5:42 PM
 */

namespace Admin\Controller;


use Application\Controller\AbstractController;
use Application\Form\SmsGatewayForm;
use Application\Model\SettingTable;
use Application\Model\SmsGatewayFieldTable;
use Application\Model\SmsGatewayTable;
use Intermatics\BaseForm;
use Intermatics\HelperTrait;
use Zend\Form\Element\Checkbox;

class SmsgatewayController extends AbstractController {

    use HelperTrait;

    public function indexAction()
    {
        //get gateways
        $table = new SmsGatewayTable($this->getServiceLocator());
        $settingsTable = new SettingTable($this->getServiceLocator());
        $rowset = $table->getRecords();
        $gateway = $table->getActiveGateway();
        $form = $this->getSmsForm();

        if($this->request->isPost()){
            $smsEnabled = $this->request->getPost('sms_enabled');
            $settingsTable->saveSetting('sms_enabled',$smsEnabled);
            $settingsTable->saveSetting('sms_sender_name',$this->request->getPost('sms_sender_name'));
            $this->flashmessenger()->addMessage('Settings changed');
            return $this->redirect()->toRoute('admin/default',['controller'=>'smsgateway','action'=>'index']);

        }

        $form->setData([
            'sms_enabled'=>$this->getSetting('sms_enabled'),
            'sms_sender_name'=>$this->getSetting('sms_sender_name')
        ]);



        return ['pageTitle'=>__('SMS GATEWAYS'),'rowset'=>$rowset,'gateway'=>$gateway,'form'=>$form];
    }


    public function customizeAction(){

        $output = [];
        $id = $this->params('id');
        $smsGatewayTable = new SmsGatewayTable($this->getServiceLocator());
        $smsFieldsTable = new SmsGatewayFieldTable($this->getServiceLocator());



        $smsGatewayForm = new SmsGatewayForm(null,$this->getServiceLocator(),$id);
        $rowset = $smsFieldsTable->getGatewayRecords($id);
        $rowset->buffer();

        if($this->request->isPost())
        {

            $formData = $this->request->getPost();
            foreach($rowset as $row){
                $smsFieldsTable->saveField($id,$row->key,trim($formData[$row->key]));
            }
            $output['message']=__('Changes Saved!');
            $this->flashmessenger()->addMessage('Gateway settings saved!');
            return $this->redirect()->toRoute('admin/default',['controller'=>'smsgateway','action'=>'index']);
            $smsGatewayForm->setData($formData);


        }
        else{
            $data = [];
            foreach($rowset as $row){
                $data[$row->key] = $row->value;
            }
            $smsGatewayForm->setData($data);

        }

        $output['options'] = $rowset;
        $output['form']=$smsGatewayForm;
        $output['pageTitle'] = __('Configure Gateway').': '.$smsGatewayTable->getRecord($id)->gateway_name;
        $output['table'] = $smsFieldsTable;
        $output['id'] = $id;

        return $output;


    }


    public function installAction(){
        $id = $this->params('id');

        $smsGatewayTable = new SmsGatewayTable($this->getServiceLocator());
        $smsGatewayTable->uninstallAll();

        $smsGatewayTable->update(['active'=>1],$id);
        $this->flashMessenger()->addMessage(__('Gateway Installed'));
        return $this->redirect()->toRoute('admin/default',['controller'=>'smsgateway','action'=>'customize','id'=>$id]);
    }


    public function uninstallAction(){
        $id = $this->params('id');
        $smsGatewayTable  = new SmsGatewayTable($this->getServiceLocator());
        $smsGatewayTable->update(['active'=>0],$id);
        $this->flashMessenger()->addMessage(__('Gateway uninstalled'));
        return $this->goBack();
    }



    private function getSmsForm(){
        $form = new BaseForm();
        $form->createCheckbox('sms_enabled','Enable SMS?',1);
        $form->createText('sms_sender_name','Sender Name',true,null,null,'Enter the default sender name of your gateway');
        $form->get('sms_sender_name')->setAttribute('maxlength','11');
        return $form;
    }





}