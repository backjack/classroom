<?php

namespace Application\Form;

use Zend\InputFilter\InputFilter;
use Zend\Validator\NotEmpty;

class SessionFilter extends InputFilter {
    function __construct() {


        $this->add(array(
            'name'=>'session_name',
            'required'=>true,
            'validators'=>array(
                array(
                    'name'=>'NotEmpty'
                )
            )
        ));

        $this->add(array(
            'name'=>'session_date',
            'required'=>true,
            'validators'=>array(
                array(
                    'name'=>'NotEmpty',
                    'options' => array(
            'messages'  => array(
                NotEmpty::IS_EMPTY => "You must specify the Session Start Date",
            ),
        ),
                )
            )
        ));

        $this->add(array(
            'name'=>'session_status',
            'required'=>true,
            'validators'=>array(
                array(
                    'name'=>'NotEmpty'
                )
            )
        ));

        $this->add(array(
            'name'=>'session_end_date',
            'required'=>false,

        ));


        $this->add(array(
            'name'=>'payment_required',
            'required'=>false,
        ));


        $this->add(array(
            'name'=>'amount',
            'required'=>false,
            'validators'=>array(
                array(
                    'name'=>'Digits'
                )
            )
        ));


        $this->add(array(
            'name'=>'enrollment_closes',
            'required'=>false,
        ));

        $this->add(array(
            'name'=>'description',
            'required'=>false,
        ));

        $this->add(array(
            'name'=>'venue',
            'required'=>false,
        ));


        $this->add(array(
            'name'=>'picture',
            'required'=>false,
        ));

        $this->add(array(
            'name'=>'session_instructor_id[]',
            'required'=>false,
        ));

        $this->add(array(
            'name'=>'enable_forum',
            'required'=>false,
        ));
        $this->add(array(
            'name'=>'enable_discussion',
            'required'=>false,
        ));
    }
}

?>