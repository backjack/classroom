<?php

namespace Admin\Controller;

require_once 'vendor/phpQuery/phpQuery.php';
use Application\Controller\AbstractController;
use Application\Model\LessonTable;
use Application\Model\SessionTable;
use Application\Model\WidgetTable;
use Intermatics\HelperTrait;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Application\Model\WidgetValueTable;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Element\Number;

/**
 * WidgetController
 *
 * @author
 *
 * @version
 *
 */
class WidgetController extends AbstractController {
	
    use HelperTrait;
    private $data = [];
	protected $acceptCriteria = array(
			'Zend\View\Model\ViewModel' => array(
					'text/html',
			),
			'Zend\View\Model\JsonModel' => array(
					'application/json',
			));
	
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
       
		$this->data['pageTitle'] = __('Homepage Widgets');
		// TODO Auto-generated WidgetController::indexAction() default action
		$widgetsTable = new WidgetTable($this->getServiceLocator());
		$widgetValueTable = new WidgetValueTable($this->getServiceLocator());
		
		//get all all avialable widgets
		$widgets = $widgetsTable->getRecords();
		$this->data['widgets'] = $widgets;
        $editors = [];
        $html = [];
		
		//create the elements for creating a new widget
		$createSelect = new Select('widget_id');
		$options = array();
		foreach ($widgets as $row){
			$options[$row->widget_id]=__($row->widget_name);
		}
		$createSelect->setAttribute('class', 'form-control');
		$createSelect->setEmptyOption('');
		$createSelect->setAttribute('required', 'required');
		$createSelect->setValueOptions($options);
		$this->data['createSelect']=$createSelect;
		//get the merchant's widgets
		$merchantWidgets = $widgetValueTable->getWidgets();
		$this->data['merchantWidgets'] = $merchantWidgets;
		$sessionTable = new SessionTable($this->getServiceLocator());
        $sessions = $sessionTable->getLimitedRecords(1000);
        $sessions->buffer();
        //create category list select
        $sessionSelect = '<select name="session[num]" class="form-control select2"  ><option></option>';


        foreach ($sessions as $row){
            $type = sessionType($row->session_type);
            $sessionSelect.= "
<option value=\"$row->session_id\">$row->session_name ($type)</option>";
        }

        $sessionSelect.= '
					</select>';


        foreach ($merchantWidgets as $row)
		{
			$form = $row->form;

            $newForm = str_replace('[sessionselect]', $sessionSelect, $form);


			
			$repeat = $row->repeat;
				
			if (!empty($repeat)) {
				$form='';
				for ($i=1;$i<=$repeat;$i++)
				{
				$append = str_replace('[num]', $i, $newForm);
				$form .= $append;
				}
			
			
			}
			
			$noImage = $this->getBaseUrl().'/img/no_image.jpg';
			$form = str_replace('[base]', $this->getBaseUrl(), $form);
			$form = str_replace('[no_image]', $noImage, $form);
			
			
			$select = new Select('enabled_'.$row->widget_value_id);
			$select->setValueOptions(array(
					'1'=>__('Enabled'),
					'0'=>__('Disabled')
			));
			$select->setAttribute('class', 'form-control');
			$select->setAttribute('name', 'enabled');
			
			$sortOrder = new Number('sortOrder_'.$row->widget_value_id);
			$sortOrder->setAttribute('class', 'form-control');
			$sortOrder->setAttribute('name', 'sort_order');
			$sortOrder->setAttribute('placeholder', 'Sort Order');
			
			$select->setValue($row->enabled);
			$sortOrder->setValue($row->sort_order);

            $visibilitySelect = new Select('visibility');
            $visibilitySelect->setAttribute('class','form-control');
            $visibilitySelect->setAttribute('name','visibility');
            $visibilitySelect->setAttribute('placeholder','Visibility');
            $visibilitySelect->setValueOptions([
               'w'=>__('Website'),
                'm'=>__('Mobile App'),
                'b'=>__('website-app')
            ]);
            $visibilitySelect->setValue($row->visibility);
			
			if (!empty($row->value)) {
				 
				$valueArray = unserialize($row->value);
				 
				$object = \phpQuery::newDocumentHTML($form);
				
				foreach ($valueArray as $key=>$value)
				{
					$object->find("[name='$key']")->val($value);
						
					if ($object->find("[data-name='$key']")) {
						if (!empty($value) && !is_array($value)) {
				
							$object->find("[data-name='$key']")->attr('src',resizeImage($value, 100, 100,$this->getBaseUrl()));
						}
						else
						{
							$object->find("[data-name='$key']")->attr('src',$noImage);
						}
				
					}
						
						
						
				}
				
				$form = $object->html();
				
			 
			
				
				
			}
			
			//modify textareas with editor classes
			if (preg_match('#rte#', $form)) {
					
				$object = \phpQuery::newDocumentHTML($form);
				$count = 1;
				foreach ($object->find('textarea.rte') as $ob)
				{
					$editor = pq($ob);
						
					$editor->attr('id',$row->widget_code.'_editor'.$count);
					$editors[] = $row->widget_code.'_editor'.$count;
					$count++;
				}
			
			
				$form = $object->html();
			}
			
			
			
				$html[$row->widget_value_id] = array(
						'form'=>$form,
						'enabled'=>$select,
						'sortOrder'=>$sortOrder, 
						'description'=>$row->widget_description,
                        'code'=>$row->widget_code,
						'name'=>$row->widget_name,
						'code'=>$row->widget_code,
                        'visibility'=>$visibilitySelect
				);
			
			
			
		}
		
		$this->data['editors'] = $editors;
		
		$this->data['form'] = $html;
		
		return new ViewModel($this->data);
	}
	
	
	public function createAction()
	{
		if($this->request->isPost())
		{
			$data = $_POST;
			$merchantMobileWidgetTable = new WidgetValueTable($this->getServiceLocator()); 
			$data['enabled']=1;
			 
			$merchantMobileWidgetTable->addRecord($data);
			
			$this->flashMessenger()->addMessage(__('Widget created!'));
		}
		$this->redirect()->toRoute('admin/default',array('controller'=>'widget','action'=>'index'));
	}
	
	public function processAction()
	{
		$viewModel = $this->acceptableViewModelSelector($this->acceptCriteria);
		$merchantTemplateOptionsTable = new WidgetValueTable($this->getServiceLocator());
		$data = $_POST;
	
	
		$optionId = $this->params()->fromRoute('id');
		$status = false;
		$message = __('Submission Failed');
		if($merchantTemplateOptionsTable->saveOptions($optionId, $data))
		{
			$status = true;
			$message = __('Changes Saved!');
		}

        exit(json_encode(array('status'=>$status,'message'=>$message)));


	}
	
	public function deleteAction(){
		$merchantTemplateOptionsTable = new WidgetValueTable($this->getServiceLocator());
		
		
		$optionId = $this->params()->fromRoute('id');
		$merchantTemplateOptionsTable->deleteRecord($optionId);
		$this->redirect()->toRoute('admin/default',array('controller'=>'widget','action'=>'index'));
		
	}
}