<?php

namespace Application\Form;

use Zend\InputFilter\InputFilter;

class DownloadFilter extends InputFilter {
    function __construct() {


        $this->add(array(
            'name'=>'download_name',
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
            )
        ));
        $this->add(array(
            'name'=>'status',
            'required'=>true,
            'validators'=>array(
            )
        ));




    }
}

?>