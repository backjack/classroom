<?php
namespace Application\Form;

use Intermatics\BaseForm;

class SurveyForm extends BaseForm
{

    public function __construct($name = null,$serviceLocator)
    {
        $this->setServiceLocator($serviceLocator);
        // we want to ignore the name passed
        parent::__construct('user');
        $this->setAttribute('method', 'post');

        $this->createText('name','Survey Name',true);
        $this->createTextArea('description','Instructions');
        $this->createSelect('status','Status',[1=>__('Enabled'),0=>__('Disabled')],true,false);
        $this->get('description')->setAttribute('id','description');
        $this->createSelect('private','Private',[0=>__('no'),1=>__('yes')]);


    }


}