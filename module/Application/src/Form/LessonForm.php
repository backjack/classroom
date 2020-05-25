<?php

namespace Application\Form;

use Application\Model\LessonGroupTable;
use Application\Model\TestTable;
use Intermatics\BaseForm;
use Zend\Form\Form;

class LessonForm extends BaseForm {
    public function __construct($name = null,$serviceLocator)
    {
        // we want to ignore the name passed
        parent::__construct('user');
        $this->setAttribute('method', 'post');



        $this->add(array(
            'name'=>'lesson_name',
            'attributes' => array(
                'type'=>'text',
                'class'=>'form-control ',
                'required'=>'required',
            ),
            'options'=>array('label'=>__('Class Name').' ('.__('Required').')'),
        ));

        $this->add(array(
            'name'=>'sort_order',
            'attributes' => array(
                'type'=>'text',
                'class'=>'form-control number',
                'placeholder'=>__('Digits only')
            ),
            'options'=>array('label'=>__('Sort Order (optional)')),
        ));

        $this->add(array(
            'name'=>'content',
            'attributes' => array(
                'type'=>'textarea',
                'class'=>'form-control ',
                'id'=>'hcontent'
            ),
            'options'=>array('label'=>__('Brief Description')),
        ));

        $this->add(array(
            'name'=>'introduction',
            'attributes' => array(
                'type'=>'textarea',
                'class'=>'form-control ',
                'id'=>'hintroduction'
            ),
            'options'=>array('label'=>__('Introduction')),
        ));


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

        $this->createSelect('lesson_type','Class Type',['s'=>__('Physical Location'),'c'=>__('Online Class')],true,true);

        $this->createCheckbox('test_required','Test Required',1);

        $testTable = new TestTable($serviceLocator);
        $rowset = $testTable->getLimitedRecords(1000);

        $options = [];
        foreach($rowset as $row)
        {
            $options[$row->test_id] = $row->name;
        }

        $this->createSelect('test_id','Required Test',$options,false);
        $this->get('test_id')->setAttribute('class','form-control select2');

        $options = [];
        $lessonGroupTable = new LessonGroupTable($serviceLocator);
        $rowset = $lessonGroupTable->getLimitedRecords(5000);
        foreach($rowset as $row){
            $options[$row->lesson_group_id]=$row->group_name;
        }
        $this->createSelect('lesson_group_id[]','Class Groups (Optional)',$options,false);
        $this->get('lesson_group_id[]')->setAttribute('multiple','multiple');
        $this->get('lesson_group_id[]')->setAttribute('class','form-control select2');

        $this->createCheckbox('enforce_lecture_order','Enforce Lecture Order',1);
        $this->get('enforce_lecture_order')->setValue(1);
    }

}

?>