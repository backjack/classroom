<?php

namespace Application\Form;

use Application\Model\SessionTable;
use Intermatics\BaseForm;
use Zend\Form\Form;

class CertificateForm extends BaseForm {
    public function __construct($name = null,$serviceLocator)
    {
        // we want to ignore the name passed
        parent::__construct('user');
        $this->setAttribute('method', 'post');



        $this->add(array(
            'name'=>'certificate_name',
            'attributes' => array(
                'type'=>'text',
                'class'=>'form-control ',
                'required'=>'required',
            ),
            'options'=>array('label'=>__('Certificate Name')),
        ));


        $this->add(array(
            'name'=>'description',
            'attributes' => array(
                'type'=>'textarea',
                'class'=>'form-control ',
                'required'=>'required',
                'id'=>'hcontent'
            ),
            'options'=>array('label'=>__('Description')),
        ));



        $this->add(array(
            'name'=>'certificate_image',
            'attributes' => array(
                'type'=>'hidden',
                'class'=>'form-control ',
                'required'=>'required',
                'id'=>'image'
            ),
            'options'=>array('label'=>__('Certificate Image')),
        ));

        $this->createSelect('orientation','Orientation',['p'=>__('Portrait'),'l'=>__('Landscape')],true,false);
        $this->createSelect('status','Enabled',[1=>__('Yes'),0=>__('No')],true,false);

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

        $this->createSelect('session_id', 'Session/Course', $options);
        $this->get('session_id')->setAttribute('class','form-control select2');
        $this->createText('max_downloads','Maximum Downloads',false,'form-control number',null,__('Digits only'));
    }

}

?>