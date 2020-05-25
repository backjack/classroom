<?php

namespace Application\Form;

use Application\Model\RegistrationFieldTable;
use Zend\Form\Element\Captcha;
use Zend\Captcha\AdapterInterface as CaptchaAdapter;
use Application\Model\AgeRangeTable;
use Application\Model\MaritalStatusTable;
use Intermatics\BaseForm;
use Zend\Form\Form;
use Zend\Form\Element;

class SignupForm extends BaseForm {
    public function __construct($name = null,$serviceLocator,CaptchaAdapter $captchaAdapter = null)
    {
        $this->setServiceLocator($serviceLocator);
        // we want to ignore the name passed
        parent::__construct('user');
        $this->setAttribute('method', 'post');
        $this->addCSRF();
        $this->translate = false;


        $this->add(array(
            'name'=>'first_name',
            'attributes' => array(
                'type'=>'text',
                'class'=>'form-control ',
                'required'=>'required',
            ),
            'options'=>array('label'=>__('Firstname')),
        ));

        $this->add(array(
            'name'=>'last_name',
            'attributes' => array(
                'type'=>'text',
                'class'=>'form-control ',
                'required'=>'required',
            ),
            'options'=>array('label'=>__('Lastname')),
        ));






        $this->add(array(
            'name'=>'mobile_number',
            'attributes' => array(
                'type'=>'tel',
                'class'=>'form-control ',
              //  'placeholder'=>'Digits only',
                'pattern'=>'\d*',
                'x-autocompletetype'=>'tel'
            ),
            'options'=>array('label'=>__('Mobile Number'),),
        ));



        $this->add(array(
            'name'=>'email',
            'attributes' => array(
                'type'=>'email',
                'class'=>'form-control ',
                'required'=>'required',
            ),
            'options'=>array('label'=>__('Email')),
        ));



        //create new form
        $registrationFieldsTable = new RegistrationFieldTable($serviceLocator);
        $rowset = $registrationFieldsTable->getActiveFields();
        foreach($rowset as $row){

            switch($row->type){

                case 'checkbox':
                    $this->createCheckbox('custom_'.$row->registration_field_id,$row->name,1);
                    break;
                case 'radio':
                    $vals = nl2br($row->options);
                    $options = explode('<br />',$vals);

                    $selectOptions =[];
                    foreach($options as $value){
                        $selectOptions[$value]=$value;
                    }

                    $this->add(array(
                        'type' => 'Zend\Form\Element\Radio',
                        'name' => 'custom_'.$row->registration_field_id,
                        'options' => array(
                            'label' => $row->name,
                            'value_options' => $selectOptions,
                        )
                    ));
                    break;
                case 'text':
                    $this->createText('custom_'.$row->registration_field_id,$row->name,!empty($row->required),null,null,$row->placeholder);
                    break;
                case 'textarea':
                    $this->createTextArea('custom_'.$row->registration_field_id,$row->name,!empty($row->required),null,$row->placeholder);
                    break;
                case 'select':
                    $vals = nl2br($row->options);
                    $options = explode('<br />',$vals);

                    $selectOptions =[];
                    foreach($options as $value){
                        $selectOptions[$value]=$value;
                    }
                    $this->createSelect('custom_'.$row->registration_field_id,$row->name,$selectOptions,!empty($row->required));
                    break;
                case 'file':
                    $file = new Element\File('custom_'.$row->registration_field_id);
                    $file->setLabel($row->name)
                        ->setAttribute('id','custom_'.$row->registration_field_id);
                    $this->add($file);

            }

        }


        $this->createPassword('password',__('Password'),true);
        $this->createPassword('confirm_password',__('Confirm Password'),true);


        //add captcha

        $captchaSetting = getSetting('regis_captcha_type');

        if($captchaSetting=='image'){
            $captcha = new Captcha('captcha');
            $captcha->setCaptcha($captchaAdapter);
            $captcha->setOptions(array('label' => __('captcha-label')));
            $this->add($captcha);
        }




    }



}

?>