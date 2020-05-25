<?php

namespace Intermatics;

use Zend\Form\Element\Csrf;
use Zend\Form\FormInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Metadata\Metadata;
use Zend\Db\Adapter\Adapter;

use Zend\Form\Form; 
use Zend\Validator\ValidatorChain;

class BaseForm extends Form {
	
 
	protected $fields; 
	protected $serviceLocator;
	protected $inputFilter;
    public $translate = true;
	
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
		$this->serviceLocator = $serviceLocator;
	}
	
	/**
	 * Fields should be of the format:
	 * 'element'=>array(
	 * 'type'=>$type,
	 * 'label'=>'label',
	 * 'required'=>'required',
	 * 'value'=>'value',
	 * 'options'=>'options')
	 * @param unknown $fields
	 */
	public function createElements($fields)
	{
		if (is_array($this->fields)) {
			$this->fields = array_merge($this->fields,$fields);
		}
		else 
		{
			$this->fields = $fields;
		}	
		$this->createFieldElements();
	}
	
	public function createFieldElements()
	{
		foreach($this->fields as $key=>$value)
		{
			 
			switch ($value['type'])
			{
			
				case 'text':
					$this->createText($key, @$value['label'],@$value['required'],@$value['class'],@$value['value'],@$value['placeholder']);
					break;
				case 'password':
						$this->createPassword($key, @$value['label'],@$value['required'],@$value['class'],@$value['value']);
					break;
				case 'hidden':
						$this->createHidden($key,@$value['value']);
					break;
				case 'textarea':
					$this->createTextArea($key,@$value['label'],@$value['required'],@$value['value'],@$value['placeholder']);
					break;
				case 'select':
					$this->createSelect($key, @$value['label'], @$value['options'],@$value['required'],@$value['emptyfirst']);
					break;
				case 'checkbox':
					$this->createCheckbox($key, @$value['label'], @$value['value']);
					break;
				default:
					$this->createText($key, @$value['label'],@$value['required'],@$value['value']);
					break;
			}
		}
	}
	
	public function createText($name,$label,$required=false,$class=null,$value=null,$placeholder=null)
	{
        if($this->translate){
            $label = __($label);
        }


		$attributes = array(
						'type'=>'text',
						'class'=>'form-control ',
						'data-options'=>'required:true', 
				);
		if ($required) {
			$attributes['required'] = 'required';
		}
		
		if (isset($placeholder)) {
			$attributes['placeholder'] = $placeholder;
		}
		
		if (isset($class))
		{
			$attributes['class'] = $class;
		}
		
		$elements = array(
				'name'=>$name,
				'attributes' => $attributes,
				'options'=>array('label'=>$label,'label_attributes'=>array('class'=>'control-label')),
		);
		
		if ($required) {
			$elements['validators']=array(
				array('name'=>'NotEmpty')
			);
		}
		
		$this->add($elements);
		
		if (isset($value)) {
			$this->get($name)->setValue($value);
		}
		
	
		
	}
	
	public function createPassword($name,$label,$required=false,$class=null,$value=null)
	{
        if($this->translate){
            $label = __($label);
        }
		$attributes = array(
				'type'=>'password',
				'class'=>'form-control ',
				'data-options'=>'required:true',
		);
		if ($required) {
			$attributes['required'] = 'required';
		}
	
		if (isset($class))
		{
			$attributes['class'] = $class;
		}
	
		$elements = array(
				'name'=>$name,
				'attributes' => $attributes,
				'options'=>array('label'=>$label,'label_attributes'=>array('class'=>'control-label')),
		);
	
		if ($required) {
			$elements['validators']=array(
					array('name'=>'NotEmpty')
			);
		}
	
		$this->add($elements);
	
		if (isset($value)) {
			$this->get($name)->setValue($value);
		}
	
	
	
	}
	
	public function createHidden($name,$value=null)
	{
		$attributes = array(
				'type'=>'hidden',
				'value'=>$value 
		); 
		$this->add(array(
				'name'=>$name,
				'attributes' => $attributes,
		));
	
		if (isset($value)) {
			$this->get($name)->setValue($value);
			
		}
		 
	}
	
	public function createTextArea($name,$label,$required=false,$value=null,$placeholder=null)
	{
        if($this->translate){
            $label = __($label);
        }
		$attributes = array(
				'type'=>'textarea',
				'class'=>'form-control ',
				'data-options'=>'required:true',
		);
		if ($required) {
			$attributes['required'] = 'required';
		}

		if (isset($placeholder)) {
			$attributes['placeholder'] = $placeholder;
		}
		
		
		$elements = array(
				'name'=>$name,
				'attributes' => $attributes,
				'options'=>array('label'=>$label,'label_attributes'=>array('class'=>'control-label')),
		);
		
		if ($required) {
			$elements['validators']=array(
					array('name'=>'NotEmpty')
			);
		}
		
		$this->add($elements);
		
		
		if (isset($value)) {
			$this->get($name)->setValue($value);
		}
	}
	
	public function createSelect($name,$label,$options,$required=false,$emptyFirst=true)
	{
        if($this->translate){
            $label = __($label);
        }
		
		 $selectOptions = array();
		foreach ($options as $key=>$value)
		{
			$selectOptions[$key]=$value;
		}
		 
		$attributes = array(
				'type'=>'select',
				'class'=>'form-control',
				'data-options'=>'required:true',
		);
		 
		if ($required) {
			$attributes['required'] = 'required';
		}
	 
		
		$optionsValues = array(
			'label'=>$label,'label_attributes'=>array('class'=>'control-label'),'value_options'=>$selectOptions
		);
		if ($emptyFirst || !isset($emptyFirst)) {
			//$selectOptions = array(''=>'') ;
			$optionsValues['empty_option'] = '';
		}
		
		
		$elements = array(
				'name'=>$name,
				'type'=>'Zend\Form\Element\Select',
				'attributes' => $attributes, 
				'options'=>$optionsValues, 
		);
		
		if ($required) {
		 
			$elements['validators']=array(
					array('name'=>'NotEmpty')
			);
			 
		}
		else{
			//$validator = new ValidatorChain();
			//$filter = $this->getInputFilter()->get($name)->setValidatorChain($validator);
			
		}
		
	 
		$this->add($elements);
		
		
		
		  
	}
	
	public function createCheckbox($name,$label,$value)
	{
        if($this->translate){
            $label = __($label);
        }
		$this->add(array(
				'name'=>$name,
				'type' => 'Zend\Form\Element\Checkbox', 
				'options'=>array('label'=>$label,'label_attributes'=>array('class'=>'control-label'),'checked_value'=>$value,'unchecked_value'=>0),
		));
		 
	 
	}
	
 	public function getFields()
 	{
 		return $this->fields;
 	}

    public function addCSRF()
    {
        $csrf = new Csrf('security');
        $this->add($csrf);
    }

    public function getData($flag = FormInterface::VALUES_NORMALIZED){
        $data = parent::getData($flag);
        unset($data['security']);
        return $data;
    }
}

?>