<?php

namespace Admin\Controller;

use Application\Controller\AbstractController;
use Intermatics\HelperTrait;
use Zend\Form\Element\Select;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\HomeworkTable;
use Application\Form\HomeworkForm;
use Application\Form\HomeworkFilter;
use Intermatics\UtilityFunctions;
use Application\Form\ArticleForm;
/**
 * HomeworkController
 *
 * @author
 *
 * @version
 *
 */
class HomeworkController extends AbstractController {
 
	use HelperTrait;
	public function indexAction() {
		// TODO Auto-generated ArticlesController::indexAction() default action
		$table = new HomeworkTable($this->getServiceLocator());
	
		$paginator = $table->getPaginatedRecords(true);
	
		$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
		$paginator->setItemCountPerPage(30);
		return new ViewModel (array(
				'paginator'=>$paginator,
				'pageTitle'=>__('Revision Notes'),
		));
	
		 
	}
	
	public function addAction()
	{
	 
		$output = array();
		$articlesTable = new HomeworkTable($this->getServiceLocator());
		$form = new HomeworkForm(null,$this->getServiceLocator());
		 
		$filter = new HomeworkFilter();
	
		if ($this->request->isPost()) {
	
			$form->setInputFilter($filter);
			$data = $this->request->getPost();
            $data['lesson_id'] = str_ireplace('string:','',$data['lesson_id']);


			$form->setData($data);
			if ($form->isValid()) {
	
				$array = $form->getData();
                $array['lesson_id'] = intval($data['lesson_id']);

				$array[$articlesTable->getPrimary()]=0;
				$array['date']=time();
				$articlesTable->saveRecord($array);
				//    $this->flashmessenger()->addMessage(__('Changes Saved!'));
				$output['message'] = __('Record Added!');
				$form = new HomeworkForm(null,$this->getServiceLocator());

                if(!empty($data['notify'])){
                    $subject = __('New revision note');
                    $message = __('revision-note-mail',['title'=>$data['title'],'description'=>$data['description']]);
                    $sms = __('revision-note-sms',['title',$data['title']]);
                    $this->notifySessionStudents($data['session_id'],$subject,$message,true,$sms);
                }
			}
			else{
				$output['message'] = __('save-failed-msg');
			}
	
		}


	
		$output['form'] = $form;
		$output['pageTitle']= __('Add Note');
		$output['action']='add';
		$output['id']=null;
		return new ViewModel($output);
	}
	
	public function editAction(){
		$output = array();
		$articleTable = new HomeworkTable($this->getServiceLocator());
		$form = new HomeworkForm(null,$this->getServiceLocator());
		$filter = new HomeworkFilter();
		$id = $this->params('id');

		$row = $articleTable->getRecord($id);
        $oldName = $row->title;
		if ($this->request->isPost()) {
	
			$form->setInputFilter($filter);
			$data = $this->request->getPost();
            $data['lesson_id'] = str_ireplace('string:','',$data['lesson_id']);


            $form->setData($data);
			if ($form->isValid()) {
					
	
	
				$array = $form->getData();
                $array['lesson_id']=$data['lesson_id'];
				$array[$articleTable->getPrimary()]=$id;
				$articleTable->saveRecord($array);
				//    $this->flashmessenger()->addMessage(__('Changes Saved!'));
				$output['message'] = __('Changes Saved!');

                if(!empty($data['notify'])){
                    $subject = __('Updated revision note');
                    $message= __('revision-note-updated-mail',['name'=>$oldName]);
                    $this->notifySessionStudents($data['session_id'],$subject,$message);
                }
	
			}
			else{
				$output['message'] = __('save-failed-msg');
			}
	
		}
		else {
	
			$data = UtilityFunctions::getObjectProperties($row);
			$form->setData($data);
	
		}
	
		$output['form'] = $form;
		$output['id'] = $id;
		$output['pageTitle']= __('Edit Note');
		$output['row']= $row;
		$output['action']='edit';

		$viewModel = new ViewModel($output);
		$viewModel->setTemplate('admin/homework/add');
		return $viewModel ;
	
	}
	
	
	
	public function deleteAction()
	{
		$table = new HomeworkTable($this->getServiceLocator());
		$id = $this->params('id');
		$table->deleteRecord($id);
		$this->flashmessenger()->addMessage(__('Record deleted'));
		$this->redirect()->toRoute('admin/default',array('controller'=>'homework','action'=>'index'));
	}
	
}