<?php

namespace Application\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Intermatics\UtilityFunctions;

class Student implements InputFilterAwareInterface {
	 
	/*
	 * IMPORTANT!!!
	 * Make all database fields public but other properties protedted
	 */
	
	protected $inputFilter;
	
	//DATABASE FIELDS
	public $student_id;
	public $first_name;
	public $last_name;
	public $home_address;
	public $mobile_number;
	public $email;
	public $username;
	public $password;
    public $gender;
    public $marital_status_id;
    public $age_range_id;

	
	
	public function exchangeArray($data)
	{
		
		$fields = UtilityFunctions::getObjectProperties($this);
		
		$exclude = array('password');
		
		foreach ($fields as $key=>$value)
		{ 
			if (!in_array($key, $exclude)) {
				$this->$key = (isset($data[$key])) ? $data[$key] : null;
			}
			
		}
		 
	
		if (isset($data["merchant_password"]))
		{
			$this->setPassword($data["password"]);
		}
	 
	}
	
	
	public function getInputFilter()
	{
		if (!$this->inputFilter)
		{
			$inputFilter = new InputFilter();
			$factory = new InputFactory();
			 
			 
			$inputFilter->add($factory->createInput(array(
					'name'     => 'parent_id',
					'required' => false,
					'filters'  => array(
							array('name' => 'Int'),
					),
			)));
			 
			 
			$inputFilter->add($factory->createInput(array(
					'name'     => 'first_name',
					'required' => true,
					'validators'=> array(
							array(
									'name'=>'NotEmpty'
							)
					),
			)));
			 
			 
			$inputFilter->add($factory->createInput(array(
					'name'     => 'last_name',
					'required' => true,
					'validators'=> array(
							array(
									'name'=>'NotEmpty'
							)
					),
			)));
			
			$inputFilter->add($factory->createInput(array(
					'name'     => 'merchant_tel',
			)));
			 
			 
			$inputFilter->add($factory->createInput(array(
					'name'     => 'merchant_email',
					'required' => true,
					'validators'=> array(
							array(
									'name'=>'NotEmpty'
							),
							array(
									'name'=>'EmailAddress'
							),
					),
			)));
			 
			 
			$inputFilter->add($factory->createInput(array(
					'name'     => 'merchant_password',
					'required' => false,
					'validators'=> array(
							array(
									'name'=>'NotEmpty'
							),
							array(
									'name'=>'StringLength',
									'options'=>array(
										'min'=>'6',
										'max'=>128,
									),
							),
					),
			)));
			 
			$inputFilter->add($factory->createInput(array(
					'name'     => 'confirm_password',
					'required' => false,
					'validators'=>array(
							array(
									'name'=>'identical',
									'options'=>array('token'=>'merchant_password')
							),
							 
					),
			)));
			 
			 
			 
			 
			$this->inputFilter = $inputFilter;
			 
		}
	
		return $this->inputFilter;
	
	}

	
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}
	
	public function setPassword($clearPassword)
	{
		$this->password = md5($clearPassword);
	}
	
	
	
}

?>