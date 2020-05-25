<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 2/22/2018
 * Time: 2:50 PM
 */

namespace Application\Form;


use Application\Model\SmsGatewayFieldTable;
use Intermatics\BaseForm;

class SmsGatewayForm extends BaseForm {
    public function __construct($name = null,$serviceLocator,$id)
    {
        // we want to ignore the name passed
        parent::__construct('setting');
        $this->setAttribute('method', 'post');
        $smsGatewayFieldTable = new SmsGatewayFieldTable($serviceLocator);

        $rowset = $smsGatewayFieldTable->getGatewayRecords($id);



        foreach($rowset as $row)
        {

            switch($row->type){
                case 'text':
                    $class='form-control';
                    if(!empty($row->class)){
                        $class='form-control '.$row->class;
                    }
                    $this->createText($row->key,$row->label,false,$class,null,$row->placeholder);
                    break;
                case 'textarea':
                    $this->createTextArea($row->key,$row->label,false,null,$row->placeholder);
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
                case 'image':
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
                            $foptions[$temp[0]]= $temp[1];
                        }

                    }

                    $this->add(array(
                        'type' => 'Zend\Form\Element\Radio',
                        'name' => $row->key,
                        'options' => array(
                            'label' => $row->label,
                            'value_options' => $foptions,
                        )
                    ));
                    break;
                case 'color':
                    $class='form-control colorpicker-full  ';
                    if(!empty($row->class)){
                        $class='form-control colorpicker-full  '.$row->class;
                    }
                    $this->createText($row->key,$row->label,false,$class,null,$row->placeholder);
                    $this->get($row->key)->setAttribute('style','width:80px; display: inline;');
                    break;
                case 'password':
                    $class='form-control';
                    if(!empty($row->class)){
                        $class='form-control '.$row->class;
                    }
                    $this->createPassword($row->key,$row->label,false,$class,null);
                    break;
            }



        }




    }

}