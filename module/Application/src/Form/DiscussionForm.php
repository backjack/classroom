<?php

namespace Application\Form;

use Application\Model\AgeRangeTable;
use Application\Model\MaritalStatusTable;
use Application\Model\RegistrationFieldTable;
use Application\Model\StudentSessionTable;
use Intermatics\BaseForm;
use Zend\Form\Form;

class DiscussionForm extends BaseForm {
    public function __construct($name = null,$serviceLocator,$id)
    {
        // we want to ignore the name passed
        parent::__construct('user');
        $this->setAttribute('method', 'post');
        $this->addCSRF();

        $this->createText('subject','Subject',true);
        $this->createTextArea('question','Your Question',true,null,__('Ask your question here'));

        $studentSessionTable = new StudentSessionTable($serviceLocator);
        $rowset = $studentSessionTable->getSessionInstructors($id);
        $options = [];
        $options['admins'] = __('Administrators');
        foreach($rowset as $row){
            if(!empty($row->enable_discussion)){
                $options[$row->account_id]= $row->first_name.' '.$row->last_name.' ('.$row->session_name.')';
            }

         }


        $this->createSelect('account_id[]','Recipients'.'(Admins/Instructors)',$options,true);
        $this->get('account_id[]')->setAttribute('multiple','multiple');
        $this->get('account_id[]')->setAttribute('class','form-control select2');

        $rowset = $studentSessionTable->getStudentRecords(false,$id);
        $options = [];
        foreach($rowset as $row){
            if(!empty($row->enable_discussion)){
                $options[$row->session_id] = $row->session_name;
            }

        }
        $this->createSelect('session_id','course-session-(optional)',$options,false);
        $this->get('session_id')->setAttribute('class','form-control select2');

        $this->createHidden('lecture_id','');
    }



}

?>