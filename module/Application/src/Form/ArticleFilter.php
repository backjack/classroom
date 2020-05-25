<?php

namespace Application\Form;

use Zend\InputFilter\InputFilter;

class ArticleFilter extends InputFilter {
	function __construct() {
		
		
		$this->add(array(
			'name'=>'alias',
			'required'=>false,

		));
		
		$this->add(array(
				'name'=>'article_name',
				'required'=>true,
				'validators'=>array(
						array(
								'name'=>'NotEmpty'
						)
				)
		));
		
		$this->add(array(
				'name'=>'article_content',
				'required'=>false, 
		));

        $this->add(array(
            'name'=>'top_nav',
            'required'=>false,
        ));

        $this->add(array(
            'name'=>'bottom_nav',
            'required'=>false,
        ));

        $this->add(array(
            'name'=>'parent',
            'required'=>false,
        ));


        $this->add(array(
            'name'=>'sort_order',
            'required'=>false,
            'validators'=>array(
                array(
                    'name'=>'Digits'
                )
            )
        ));


        $this->add(array(
            'name'=>'visibility',
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