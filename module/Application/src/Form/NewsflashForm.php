<?php

namespace Application\Form;

use Zend\Form\Form;

class NewsflashForm extends Form {
    public function __construct($name = null)
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
 
    	 
    }
	
}

?>