<?php

namespace Application\Form;

use Application\Model\RegistrationFieldTable;
use Zend\InputFilter\InputFilter;

class ProfileFilter extends InputFilter {
    function __construct() {


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








    }
}

?>