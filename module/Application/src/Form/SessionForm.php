<?php

namespace Application\Form;

use Application\Model\AccountsTable;
use Application\Model\LessonTable;
use Intermatics\BaseForm;
use Zend\Form\Form;

class SessionForm extends BaseForm {
    public function __construct($name = null,$serviceLocator,$type=null)
    {
        // we want to ignore the name passed
        parent::__construct('user');
        $this->setAttribute('method', 'post');



        $this->add(array(
            'name'=>'session_name',
            'attributes' => array(
                'type'=>'text',
                'class'=>'form-control',
                'required'=>'required',
            ),
            'options'=>array('label'=>__('Session Name')),
        ));

        $this->add(array(
            'name'=>'session_date',
            'attributes' => array(
                'type'=>'text',
                'class'=>'form-control date',
            ),
            'options'=>array('label'=>__('Session Date')),
        ));

        $this->add(array(
            'name'=>'session_end_date',
            'attributes' => array(
                'type'=>'text',
                'class'=>'form-control date',
            ),
            'options'=>array('label'=>__('Session End Date')),
        ));

        $this->createSelect('payment_required','Payment Required',['0'=>__('No'),'1'=>__('Yes')],true,false);
        $this->createText('amount','Session Fee',false,'form-control digit',null,__('digits-only-optional'));
        $this->createSelect('session_status','Status',array('0'=>__('Disabled'),'1'=>__('Enabled')),true,false);
        $this->createTextArea('short_description','Short Description');
        $this->add(array(
            'name'=>'enrollment_closes',
            'attributes' => array(
                'type'=>'text',
                'class'=>'form-control date',
            ),
            'options'=>array('label'=>__('Enrollment Closes')),
        ));


        $this->createTextArea('description','Description');
        $this->get('description')->setAttribute('id','description');
        $this->createTextArea('venue','Venue');


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

        $accountsTable = new AccountsTable($serviceLocator);
        $rowset = $accountsTable->getRecordsSorted();
        $options = [];
        foreach($rowset as $row){
            $options[$row->account_id]= $row->first_name.' '.$row->last_name.' ('.$row->email.')';
        }

        $this->createSelect('session_instructor_id[]','Course Instructors (Optional)',$options,false);
        $this->get('session_instructor_id[]')->setAttribute('multiple','multiple');
        $this->get('session_instructor_id[]')->setAttribute('class','form-control select2');
        $this->createSelect('enable_forum','Enable Forum',['1'=>__('Yes'),'0'=>__('No')],true,false);
        $this->createSelect('enable_discussion','Enable Discussions',['1'=>__('Yes'),'0'=>__('No')],true,false);


    }

}

?>