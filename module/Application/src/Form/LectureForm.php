<?php

namespace Application\Form;

use Intermatics\BaseForm;
use Zend\Form\Form;

class LectureForm extends BaseForm {
    public function __construct($name = null,$serviceLocator)
    {
        // we want to ignore the name passed
        parent::__construct('user');
        $this->setAttribute('method', 'post');



        $this->add(array(
            'name'=>'lecture_title',
            'attributes' => array(
                'type'=>'text',
                'class'=>'form-control ',
                'required'=>'required',
            ),
            'options'=>array('label'=>__('Lecture Title')),
        ));

        $this->add(array(
            'name'=>'sort_order',
            'attributes' => array(
                'type'=>'text',
                'class'=>'form-control number',
                'placeholder'=>'Digits only'
            ),
            'options'=>array('label'=>__('Sort Order').' ('.__('optional').')'),
        ));






    }

}

?>