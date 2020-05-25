<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/7/2017
 * Time: 12:32 PM
 */

namespace Application\Form;


use Zend\InputFilter\InputFilter;

class PasswordResetFilter  extends InputFilter  {
    function __construct() {


        $this->add(array(
            'name'     => 'password',
            'required' => false,
            'validators'=> array(
                array(
                    'name'=>'NotEmpty'
                ),
                array(
                    'name'=>'StringLength',
                    'options'=>array(
                        'min'=>'6',
                        'max'=>128,
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name'     => 'confirm_password',
            'required' => false,
            'validators'=>array(
                array(
                    'name'=>'identical',
                    'options'=>array('token'=>'password')
                ),

            ),
        ));








    }
}