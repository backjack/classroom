<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 1/19/2017
 * Time: 4:09 PM
 */

namespace Application\Form;


use Application\Model\PaymentMethodFieldTable;
use Intermatics\BaseForm;

class PaymentMethodForm extends BaseForm {


    public function __construct($name = null,$serviceLocator,$id)
    {
        // we want to ignore the name passed
        parent::__construct('setting');
        $this->setAttribute('method', 'post');
        $table = new PaymentMethodFieldTable($serviceLocator);
        $rowset = $table->getRecordsForMethod($id);


        foreach ($rowset as $row) {

            switch ($row->type) {
                case 'text':
                    $this->createText($row->key, $row->label, false, null, null, $row->placeholder);
                    break;
                case 'textarea':
                    $this->createTextArea($row->key, $row->label, false, null, $row->placeholder);
                    if ($row->class == 'rte') {
                        $this->get($row->key)->setAttribute('id', 'rte_' . $row->key);
                    }

                    break;
                case 'hidden':
                    $this->add(array(
                        'name' => $row->key,
                        'attributes' => array(
                            'type' => 'hidden',
                            'id' => $row->key
                        ),
                        'options' => array(
                            'label' => $row->label,
                        ),
                    ));
                    break;
                case 'select':
                    if (!empty($row->options)) {
                        $options = explode(',', $row->options);
                        $foptions = [];

                        foreach ($options as $option) {
                            $temp = explode('=', $option);
                            $foptions[$temp[0]] = $temp[1];
                        }

                    } else {
                        $foptions = [];
                    }
                    $this->createSelect($row->key, $row->label, $foptions, false, false);
                    break;
                case 'checkbox':
                    $this->createCheckbox($row->key, $row->label, 1);
                    break;
                case 'radio':
                    $foptions = [];
                    if (!empty($row->options)) {
                        $options = explode(',', $row->options);


                        foreach ($options as $option) {
                            $temp = explode('=', $option);
                            $foptions[$temp[0]] = $temp[1];
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

        $this->createCheckbox('is_global','All Currencies?',1);
        $this->createSelect('status','Status',[0=>__('Disabled'),1=>__('Enabled')],true,false);
        $this->createText('sort_order','Sort Order',false,'number form-control',null,__('Optional. Digits only'));
        $this->createText('method_label','Label',true,null,null,__('payment-method-hint'));

    }
}