<?php

namespace Application\Form;

use Application\Model\RegistrationFieldTable;
use Zend\InputFilter\InputFilter;

class DiscussionFilter extends InputFilter {
    function __construct() {


        $this->add(array(
            'name'=>'subject',
            'required'=>true,
            'validators'=>array(
                array(
                    'name'=>'NotEmpty'
                )
            ),
            'filters'=>[

                [
                    'name'=>'StripTags'
                ]
            ]

        ));

        $this->add(array(
            'name'=>'question',
            'required'=>true,
            'validators'=>array(
                array(
                    'name'=>'NotEmpty'
                )
            )
        ));

        $this->add(array(
            'name'=>'account_id[]',
            'required'=>false,
        ));

        $this->add(array(
            'name'=>'session_id',
            'required'=>false,
        ));








    }
}

?>