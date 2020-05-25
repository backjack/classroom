<?php

namespace Application\Form;

use Zend\InputFilter\InputFilter;

class CertificateFilter extends InputFilter {
    function __construct() {


        $this->add(array(
            'name'=>'certificate_name',
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



        $this->add(array(
            'name'=>'certificate_image',
            'required'=>true,
            'validators'=>array(
                array(
                    'name'=>'NotEmpty'
                )
            )
        ));

        $this->add(array(
            'name'=>'orientation',
            'required'=>true,
            'validators'=>array(
                array(
                    'name'=>'NotEmpty'
                )
            )
        ));

        $this->add(array(
            'name'=>'max_downloads',
            'required'=>false,
            'validators'=>array(
                array(
                    'name'=>'Digits'
                )
            )
        ));

    }
}

?>