<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 2/1/2017
 * Time: 1:55 PM
 */

namespace Application\Form;


use Application\Model\SessionInstructorTable;
use Application\Model\SessionTable;
use Intermatics\BaseForm;

class TestForm extends BaseForm{

    public function __construct($name = null,$serviceLocator)
    {
        $this->setServiceLocator($serviceLocator);
        // we want to ignore the name passed
        parent::__construct('user');
        $this->setAttribute('method', 'post');

        $this->createText('name','Test Name',true);
        $this->createTextArea('description','Instructions');
        $this->createSelect('status','Status',[1=>__('Enabled'),0=>__('Disabled')],true,false);
        $this->createText('minutes','time-allowed-mins',null,'form-control number',null,__('digits-only-no-limit'));
        $this->createSelect('allow_multiple','Allow Multiple Attempts',['1'=>__('Yes'),'0'=>__('No')],true,false);
        $this->get('description')->setAttribute('id','description');
        $this->createText('passmark','passmark-percent',true,'form-control number',null,__('Digits Only'));

        /*
        $sessionTable = new SessionTable($serviceLocator);
        $sessions = $sessionTable->getPaginatedRecords(true);
        $sessions->setCurrentPageNumber(1);
        $sessions->setItemCountPerPage(500);
        $options=array();
        foreach ($sessions as $row)
        {
            $options[$row->session_id]=$row->session_name;
        }

        $sessionInstructorTable = new SessionInstructorTable($serviceLocator);
        $rowset = $sessionInstructorTable->getAccountRecords(ADMIN_ID);
        foreach($rowset as $row){
            $options[$row->session_id] = $row->session_name;
        }

        $this->createSelect('session_id', 'Session/Course (Optional)', $options);
        $this->get('session_id')->setAttribute('class','form-control select2');
        */
        $this->createCheckbox('private','private-test-question',1);
        $this->get('private')->setValue(1);
        $this->createCheckbox('show_result','show-result-student',1);
        $this->get('show_result')->setValue(1);

    }

}