<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 2/1/2017
 * Time: 7:00 PM
 */

namespace Application\Form;


use Intermatics\BaseForm;

class TestQuestionForm extends BaseForm {

    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('user');
        $this->setAttribute('method', 'post');

        $this->createTextArea('question','Question',true);
        $this->get('question')->setAttribute('class','form-control summernote');
        $this->createText('sort_order','Sort Order',false,'form-control number',null,'Digits only');


    }


}