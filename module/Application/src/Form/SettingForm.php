<?php

namespace Application\Form;

use Application\Model\CountryTable;
use Application\Model\SettingTable;
use Intermatics\BaseForm;
use Zend\Form\Form;

class SettingForm extends BaseForm {
    public function __construct($name = null,$serviceLocator)
    {
        // we want to ignore the name passed
        parent::__construct('setting');
        $this->setAttribute('method', 'post');
        $settingTable = new SettingTable($serviceLocator);
        $countryTable = new CountryTable($serviceLocator);
        $rowset = $settingTable->getRecords();



        foreach($rowset as $row)
        {
            $placeholder=$row->placeholder;
                if(!empty($placeholder)){
                    $placeholder = __($row->key.'-plac');
                }
            switch($row->type){
                case 'text':
                    $class='form-control';
                    if(!empty($row->class)){
                        $class='form-control '.$row->class;
                    }

                    $this->createText($row->key,$row->label,false,$class,null,$placeholder);
                    break;
                case 'textarea':
                    $this->createTextArea($row->key,$row->label,false,null,$placeholder);
                    if($row->class=='rte'){
                        $this->get($row->key)->setAttribute('id','rte_'.$row->key);
                    }

                    break;
                case 'hidden':
                    $this->add(array(
                        'name' => $row->key,
                        'attributes' => array(
                            'type'  => 'hidden',
                            'id'=>$row->key
                        ),
                        'options' => array(
                            'label' => $row->label,
                        ),
                    ));
                    break;
                case 'select':
                    if(!empty($row->options)){
                        $options = explode(',',$row->options);
                        $foptions = [];

                        foreach($options as $option){
                            if(preg_match('#=#',$option)) {
                                $temp = explode('=', $option);
                                $foptions[$temp[0]] = $temp[1];
                            }
                            else{
                                $foptions[$option]=$option;
                            }

                        }

                    }
                    else{
                        $foptions=[];
                    }
                    $this->createSelect($row->key,$row->label,$foptions,false,false);
                    break;
                case 'checkbox':
                    $this->createCheckbox($row->key,$row->label,1);
                    break;
                case 'radio':
                    $foptions = [];
                    if(!empty($row->options)){
                        $options = explode(',',$row->options);


                        foreach($options as $option){
                            $temp = explode('=',$option);
                            $foptions[$temp[0]]= __($temp[1]);
                        }

                    }

                    $this->add(array(
                        'type' => 'Zend\Form\Element\Radio',
                        'name' => $row->key,
                        'options' => array(
                            'label' => __($row->label),
                            'value_options' => $foptions,
                        )
                    ));
                    break;

            }



        }

        $countries = [];
        $rowset = $countryTable->getRecords();
        foreach($rowset as $row){
            $countries[$row->country_id] = $row->name.'/'.$row->currency_code;
        }

        $this->get('country_id')->setValueOptions($countries);

        $elements = $this->getElements();
        foreach($elements as $element){
           if(preg_match('#color_#',$element->getName())){
               $element->setAttribute('class','colorpicker-full form-control');
               $element->setAttribute('style','width:80px; display: inline;');
           }

        }

    }

}

?>