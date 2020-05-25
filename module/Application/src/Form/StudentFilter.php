<?php

namespace Application\Form;

use Application\Model\RegistrationFieldTable;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Filter\File\RenameUpload;
use Zend\Validator\File\Size;
use Zend\Validator\File\Extension;

class StudentFilter extends InputFilter {
	function __construct($serviceLocator,$activeOnly=false) {
		
				
		$this->add(array(
			'name'=>'first_name',
			'required'=>true,
			'validators'=>array(
			array(
				'name'=>'NotEmpty'
			)
		)
		));
		
		$this->add(array(
				'name'=>'last_name',
				'required'=>true,
				'validators'=>array(
						array(
								'name'=>'NotEmpty'
						)
				)
		));

        $this->add(array(
				'name'       => 'email',
				'required'   => false,
				'validators' => array(
						array(
								'name'    => 'EmailAddress',
								'options' => array(
										'domain' => true,
								),
						),
				),
		));
		
		$this->add(array(
				'name'=>'status',
				'required'=>true,
				'validators'=>array(
						array(
								'name'=>'NotEmpty'
						)
				)
		));

        $input = new Input('picture');
        $input->setRequired(false);
        $input->getValidatorChain()
            ->attach(new Size(5000000))
            ->attach(new Extension('jpg,png,gif,jpeg'));

        $this->add($input);

        $table= new RegistrationFieldTable($serviceLocator);

        if($activeOnly)
        {
            $rowset= $table->getActiveFields();
        }
        else{
            $rowset = $table->getAllFields();
        }

        foreach($rowset as $row)
        {
             //validate file
            if($row->type=='file'){
                $input = new Input('custom_'.$row->registration_field_id);
                $input->setRequired(!empty($row->required));
                $input->getValidatorChain()
                    ->attach(new Size(5000000))
                    ->attach(new Extension('jpg,mp4,mp3,avi,xls,7z,mdb,mdbx,csv,xlsx,txt,zip,doc,docx,pptx,pdf,ppt,png,gif,jpeg'));

                $this->add($input);
            }
            else{
                $form = array(
                    'name'=>'custom_'.$row->registration_field_id,
                    'required'=>!empty($row->required),
                );
                if(!empty($row->required)){
                    $form['validators'] = array(['name'=>'NotEmpty']);
                }
                $this->add($form);
            }




        }



 
				
	}
}

?>