<?php
/**
 * Created by PhpStorm.
 * User: ayokunle
 * Date: 6/22/18
 * Time: 1:22 PM
 */

namespace Admin\Controller;


use Application\Controller\AbstractController;
use Application\Entity\EmailTemplate;
use Application\Entity\SmsTemplate;
use Intermatics\BaseForm;
use Intermatics\HelperTrait;
use Zend\Form\Element\Email;

class MessagesController extends AbstractController
{
    use HelperTrait;

    public function emailsAction(){

        $this->data['pageTitle'] = __('Email Notifications');
        $this->data['templates'] = EmailTemplate::paginate(10);

        return $this->bladeView('admin.messages.emails',$this->data);
    }

    public function editemailAction(){
        $id = $this->params('id');
        $emailTemplate= EmailTemplate::find($id);

        if($this->request->isPost()){
            $data = $this->request->getPost();

            if(!empty($data['message']) && !empty($data['subject'])){
                $emailTemplate->message = $data['message'];
                $emailTemplate->subject = $data['subject'];
                $emailTemplate->save();
                $this->data['message'] = __('Changes Saved!');
            }

        }


        $this->data['pageTitle'] = __("Edit Email").": ".__('e-template-name-'.$id);
        $this->data['template'] = $emailTemplate;
        return $this->bladeView('admin.messages.edit-email',$this->data);

    }


    public function resetemailAction(){
        $id = $this->params('id');
        $template = EmailTemplate::find($id);

        $template->message = $template->default;
        $template->subject = $template->default_subject;
        $template->save();
        $this->flashMessenger()->addMessage(__('Email reset completed'));
        return $this->goBack();


    }








    public function smsAction(){

        $this->data['pageTitle'] = __('SMS Notifications');
        $this->data['templates'] = SmsTemplate::paginate(10);

        return $this->bladeView('admin.messages.sms',$this->data);
    }

    public function editsmsAction(){
        $id = $this->params('id');
        $smsTemplate= SmsTemplate::find($id);

        if($this->request->isPost()){
            $data = $this->request->getPost();

            if(!empty($data['message'])){
                $smsTemplate->message = $data['message'];
                $smsTemplate->save();
                $this->data['message'] = __('Changes Saved!');
            }

        }


        $this->data['pageTitle'] = __("Edit SMS").": ".__('s-template-name-'.$id);
        $this->data['template'] = $smsTemplate;
        return $this->bladeView('admin.messages.edit-sms',$this->data);

    }


    public function resetsmsAction(){
        $id = $this->params('id');
        $template = SmsTemplate::find($id);

        $template->message = $template->default;
        $template->save();
        $this->flashMessenger()->addMessage(__('SMS reset completed'));
        return $this->goBack();


    }
}