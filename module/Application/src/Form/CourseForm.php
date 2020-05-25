<?php

namespace Application\Form;

use Application\Model\AccountsTable;
use Application\Model\LessonTable;
use Application\Model\SessionCategoryTable;
use Intermatics\BaseForm;
use Zend\Form\Form;

class CourseForm extends BaseForm {
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
            'options'=>array('label'=>__('Course Name')),
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

        $this->add(array(
            'name'=>'enrollment_closes',
            'attributes' => array(
                'type'=>'text',
                'class'=>'form-control date',
            ),
            'options'=>array('label'=>__('Enrollment Closes')),
        ));


        $this->createSelect('payment_required','Payment Required',['0'=>__('No'),'1'=>__('Yes')],true,false);
        $this->createText('amount','Course Fee',false,'form-control digit',null,__('digits-only-optional'));
        $this->createSelect('session_status','Status',array('0'=>__('Disabled'),'1'=>__('Enabled')),true,false);




        $this->createTextArea('description','Description');
        $this->get('description')->setAttribute('id','description');


/*        $lessonsTable = new LessonTable($serviceLocator);
        $rowset = $lessonsTable->getLimitedLessonRecords($type,5000);
        foreach($rowset as $row)
        {
            //    $this->createCheckbox('lesson_'.$row->lesson_id,$row->lesson_name,$row->lesson_id);

            $this->add(array(
                'name'=>'lesson_'.$row->lesson_id,
                'type' => 'Zend\Form\Element\Checkbox',
                'attributes'=> ['class'=>'cbox'],
                'options'=>array('label'=>$row->lesson_name,'label_attributes'=>array('class'=>'control-label'),'checked_value'=>$row->lesson_id,'unchecked_value'=>0,'disable_inarray_validator' => true),
            ));
            $this->createText('lesson_date_'.$row->lesson_id,'Date',false,'date form-control',null,'Opening Date (optional)');
            $this->createText('sort_order_'.$row->lesson_id,'Sort Order',false,'number sort_field form-control',$row->sort_order);

        }*/

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


        $this->createSelect('enable_discussion','Enable Discussions',['1'=>__('Yes'),'0'=>__('No')],true,false);
        $this->createSelect('enable_forum','Enable Forum',['1'=>__('Yes'),'0'=>__('No')],true,false);

        $this->createSelect('enable_chat','Enable Live Chat',['1'=>__('Yes'),'0'=>__('No')],true,false);

        $this->createSelect('enforce_order','Enforce Class Order',['0'=>__('No'),'1'=>__('Yes')],true,false);

        $options = [];
        $sessionCategoryTable = new SessionCategoryTable($serviceLocator);
        $rowset = $sessionCategoryTable->getLimitedRecords(5000);
        foreach($rowset as $row){
            $options[$row->session_category_id]=$row->category_name;
        }
        $this->createSelect('session_category_id[]','Course Categories (optional)',$options,false);
        $this->get('session_category_id[]')->setAttribute('multiple','multiple');
        $this->get('session_category_id[]')->setAttribute('class','form-control select2');

        $this->createText('effort','Effort',false,null,null,__('six-hours-per-week'));
        $this->createText('length','Length',false,null,null,__('ten-weeks'));
        $this->createTextArea('short_description','Short Description',true);
        $this->createTextArea('introduction','introduction',false);
        $this->get('introduction')->setAttribute('id','introduction');

        $accountsTable = new AccountsTable($serviceLocator);
        $rowset = $accountsTable->getRecordsSorted();
        $options = [];
        foreach($rowset as $row){
            $options[$row->account_id]= $row->first_name.' '.$row->last_name.' ('.$row->email.')';
        }

        $this->createSelect('session_instructor_id[]','course-instructors-(optional)',$options,false);
        $this->get('session_instructor_id[]')->setAttribute('multiple','multiple');
        $this->get('session_instructor_id[]')->setAttribute('class','form-control select2');

    }

}

?>