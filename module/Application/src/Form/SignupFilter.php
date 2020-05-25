<?php

namespace Application\Form;

use Application\Model\RegistrationFieldTable;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Filter\File\RenameUpload;
use Zend\Validator\File\Size;
use Zend\Validator\File\Extension;

class SignupFilter extends InputFilter {
    function __construct($serviceLocator) {


        $this->add(array(
            'name'=>'first_name',
            'required'=>true,
            'validators'=>array(
                array(
                    'name'=>'NotEmpty'
                )
            )
        ));

        $this->add(array(
            'name'=>'last_name',
            'required'=>true,
            'validators'=>array(
                array(
                    'name'=>'NotEmpty'
                )
            )
        ));



        $this->add(array(
            'name'       => 'email',
            'required'   => true,
            'validators' => array(
                array(
                    'name'    => 'EmailAddress',
                    'options' => array(
                        'domain' => true,
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name'       => 'mobile_number',
            'required'   => true,
            'validators' => array(
                array(
                    'name'    => 'Digits',
                    'options' => array(
                        'domain' => true,
                    ),
                ),
            ),
        ));



        $this->add(array(
            'name'=>'password',
            'required'=>true,
            'validators'=>array(
                array(
                    'name'=>'NotEmpty'
                )
            )
        ));


        $this->add(array(
            'name'=>'confirm_password',
            'required'=>true,
            'validators'=>array(
                array(
                    'name'=>'NotEmpty'
                ),
                array(
                    'name'    => 'Identical',
                    'options' => array(
                        'token' => 'password',
                    )
                )
            )
        ));


        $table= new RegistrationFieldTable($serviceLocator);
        $rowset = $table->getActiveFields();

        foreach($rowset as $row)
        {

            //validate file
            if($row->type=='file'){
                $input = new Input('custom_'.$row->registration_field_id);
                $input->setRequired(!empty($row->required));
                $input->getValidatorChain()
                    ->attach(new Size(5000000))
                    ->attach(new Extension('jpg,jpeg,png,gif,doc,docx,pdf'));


                $this->add($input);
            }
            else{
                $form = array(
                    'name'=>'custom_'.$row->registration_field_id,
                    'required'=>!empty($row->required),
                );
                if(!empty($row->required)){
                    $form['validators'] = array(['name'=>'NotEmpty']);
                }
                $this->add($form);
            }




        }




    }
}

?>