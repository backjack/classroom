<?php

namespace Application\Form;

use Application\Model\LessonTable;
use Application\Model\SessionInstructorTable;
use Application\Model\SessionTable;
use Zend\Form\Form;
use Application\Model\StudentCategoriesTable;
use Intermatics\BaseForm;

class HomeworkForm extends BaseForm {
    public function __construct($name = null,$serviceLocator)
    {
    	// we want to ignore the name passed
    	parent::__construct('user');
    	$this->setAttribute('method', 'post');
    	
    	 
    	
    	$this->add(array(
    		'name'=>'title',
    	    'attributes' => array(
    	    'type'=>'text',
    	    		'class'=>'form-control ', 
    	    		'required'=>'required',
    	        ),
    	    'options'=>array('label'=>__('Title')),
    	));
    	
    	$this->add(array(
    			'name'=>'content',
    	    'attributes' => array(
    			'type'=>'textarea',
    	    		'class'=>'form-control ', 
    	    		'required'=>'required',
    	    		'id'=>'hcontent'
    	        ),
    			'options'=>array('label'=>__('Content')),
    	));
    	
     
    	
    	//get student categories
    	$sessionTable =new SessionTable($serviceLocator);
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
    	$this->createSelect('session_id', 'Session/Course', $options);


        $this->get('session_id')->setAttribute('class','form-control select2');



        $lessonTable = new LessonTable($serviceLocator);
        $rowset = $lessonTable->getRecords();
        $option= [];
        foreach($rowset as $row)
        {
            $option[$row->lesson_id] = $row->lesson_name;
        }

       $this->createSelect('lesson_id','Class',$option,true);


      //  $this->get('session_id')->setAttribute('data-ng-change',"loadBulkStudents()");

        $this->get('session_id')->setAttribute('required','required');
      //  $this->get('session_id')->setAttribute('data-ng-model','session_id');

      //  $this->get('lesson_id')->setAttribute('data-ng-model','lesson_id');
     //   $this->get('lesson_id')->setAttribute('data-ng-options','o.id as o.name for o in lessonList');
    	 
    	$this->add(array(
    			'name'=>'description',
    			'attributes' => array(
    					'type'=>'textarea',
    					'class'=>'form-control ',
    					'required'=>'required',
    			),
    			'options'=>array('label'=>__('Description').' ('.__('optional').')'),
    	));
    	
  
    	 
    }
	
}

?>