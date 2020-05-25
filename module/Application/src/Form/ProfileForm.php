<?php

namespace Application\Form;

use Application\Model\AgeRangeTable;
use Application\Model\MaritalStatusTable;
use Application\Model\RegistrationFieldTable;
use Intermatics\BaseForm;
use Zend\Form\Form;

class ProfileForm extends BaseForm {
    public function __construct($name = null,$serviceLocator)
    {
        $this->setServiceLocator($serviceLocator);
        // we want to ignore the name passed
        parent::__construct('user');
        $this->setAttribute('method', 'post');



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

        $this->createSelect('notify','Receive Notifications',['1'=>__('Yes'),'0'=>__('No')],true,false);

        $this->createTextArea('account_description','About');

        $this->add(array(
            'name'=>'picture',
            'attributes' => array(
                'type'=>'hidden',
                'class'=>'form-control ',
                'required'=>'required',
                'id'=>'image'
            ),
            'options'=>array('label'=>__('Picture')),
        ));

    }



}

?>