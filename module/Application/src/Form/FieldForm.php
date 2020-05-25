<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 1/12/2017
 * Time: 4:41 PM
 */

namespace Application\Form;


use Intermatics\BaseForm;

class FieldForm extends BaseForm {

    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('field');
        $this->setAttribute('method', 'post');


        $this->createText('name','Label',true,null,null,__('e.g. Home Address'));
        $this->createText('sort_order','sort-order-(optional)',false,'form-control number',null,__('Digits only'));
        $types = [
            'text'=>__('Text Field'),
            'textarea'=>__('Text Area'),
            'select'=>__('Dropdown'),
            'radio'=>__('Radio Button'),
            'checkbox'=>__('Checkbox'),
            'file'=>__('File/Image')
        ];
        $this->createSelect('type','Input Type',$types,true,true);

        $this->createTextArea('options','Options',false,null,__('enter-new-line'));
        $this->createSelect('required','Mandatory?',[1=>__('Yes'),0=>__('No')],true);
        $this->createText('placeholder','Field Hint');
        $this->createSelect('status','student-editable?',['1'=>__('Yes'),'0'=>__('No')],true,false);


    }

}