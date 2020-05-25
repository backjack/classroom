<?php

namespace Application\Form;

use Zend\InputFilter\InputFilter;

class MenuFilter extends InputFilter {
	function __construct() {
		
		
		$this->add(array(
			'name'=>'parent_id',
			'required'=>true,
			'validators'=>array(
			array(
				'name'=>'NotEmpty'
			)
		)
		));
		
		$this->add(array(
				'name'=>'name',
				'required'=>true,
				'validators'=>array(
						array(
								'name'=>'NotEmpty'
						)
				)
		));
		
		$this->add(array(
				'name'=>'description',
				'required'=>true,
				'validators'=>array(
						array(
								'name'=>'NotEmpty'
						)
				)
		));
		
		$this->add(array(
				'name'=>'location',
				'required'=>true,
				'validators'=>array(
						array(
								'name'=>'NotEmpty'
						)
				)
		));
		
		
	}
}

?>