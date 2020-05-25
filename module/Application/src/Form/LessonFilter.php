<?php

namespace Application\Form;

use Zend\InputFilter\InputFilter;

class LessonFilter extends InputFilter {
    function __construct() {


        $this->add(array(
            'name'=>'lesson_name',
            'required'=>true,
            'validators'=>array(
                array(
                    'name'=>'NotEmpty'
                )
            )
        ));

        $this->add(array(
            'name'=>'content',
            'required'=>false,

        ));

        $this->add(array(
            'name'=>'introduction',
            'required'=>false,
        ));

        $this->add(array(
            'name'=>'picture',
            'required'=>false,
        ));

        $this->add(array(
            'name'=>'sort_order',
            'required'=>false,
            'validators'=>array(
                array(
                    'name'=>'Digits'
                )
            )
        ));

        $this->add(array(
            'name'=>'lesson_type',
            'required'=>true,
            'validators'=>array(
                array(
                    'name'=>'NotEmpty'
                )
            )
        ));

        $this->add(array(
            'name'=>'test_required',
            'required'=>false,
        ));

        $this->add(array(
            'name'=>'test_id',
            'required'=>false,
        ));


        $this->add(array(
            'name'=>'lesson_group_id[]',
            'required'=>false,
        ));

        $this->add(array(
            'name'=>'enforce_lecture_order',
            'required'=>false,
        ));



    }
}

?>