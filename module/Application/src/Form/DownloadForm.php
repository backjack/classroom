<?php

namespace Application\Form;

use Application\Model\SessionTable;
use Intermatics\BaseForm;
use Zend\Form\Form;

class DownloadForm extends BaseForm {
    public function __construct($name = null,$serviceLocator)
    {
        // we want to ignore the name passed
        parent::__construct('user');
        $this->setAttribute('method', 'post');



        $this->add(array(
            'name'=>'download_name',
            'attributes' => array(
                'type'=>'text',
                'class'=>'form-control ',
                'required'=>'required',
            ),
            'options'=>array('label'=>__('Download Name')),
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



     $this->createSelect('status','Enabled',[1=>__('Yes'),0=>__('No')],true,false);



    }

}

?>