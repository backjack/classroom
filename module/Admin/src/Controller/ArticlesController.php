<?php

namespace Admin\Controller;

use Application\Controller\AbstractController;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\ArticlesTable;
use Application\Form\ArticleForm;
use Application\Form\ArticleFilter;
use Intermatics\UtilityFunctions;

/**
 * ArticlesController
 *
 * @author
 *
 * @version
 *
 */
class ArticlesController extends AbstractController {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		// TODO Auto-generated ArticlesController::indexAction() default action
		$table = new ArticlesTable($this->getServiceLocator()); 
          
        $paginator = $table->getPaginatedRecords(true);
        
        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30); 
        return new ViewModel (array(
        		'paginator'=>$paginator,
        		'pageTitle'=>__('Articles'),
                'articleTable'=>$table
        ));
        
         
	}
	
	public function addAction()
	{
		$output = array();
		$articlesTable = new ArticlesTable($this->getServiceLocator());
		$form = new ArticleForm(null,$this->getServiceLocator());
		$filter = new ArticleFilter();
		
		if ($this->request->isPost()) {
		
			$form->setInputFilter($filter);
			$data = $this->request->getPost();
			$form->setData($data);
			if ($form->isValid()) {
		
				$array = $form->getData();
                if(!empty($array['alias'])){
                    $array['alias'] = $articlesTable->getValidAlias(safeUrl($array['alias']));
                }
                else{
                    $array['alias'] = $articlesTable->getValidAlias(safeUrl($data['article_name']));
                }
                $array['alias'] = strtolower($array['alias']);



				$array[$articlesTable->getPrimary()]=0; 
				$articlesTable->saveRecord($array);
				//    $this->flashmessenger()->addMessage(__('Changes Saved!'));
				$output['message'] = __('Record Added!');
				$form = new ArticleForm(null,$this->getServiceLocator());
			}
			else{
				$output['message'] = __('save-failed-msg');
			}
		
		}


		$output['form'] = $form;
		$output['pageTitle']= __('Add Article');
		$output['action']='add';
		$output['id']=null;
		return new ViewModel($output);
	}
	
	public function editAction(){
		$output = array();
		$articleTable = new ArticlesTable($this->getServiceLocator());

		$filter = new ArticleFilter();
		$id = $this->params('id');
        $form = new ArticleForm(null,$this->getServiceLocator(),$id);
		
		$row = $articleTable->getRecord($id);
		if ($this->request->isPost()) {
		
			$form->setInputFilter($filter);
			$data = $this->request->getPost();
			$form->setData($data);
			if ($form->isValid()) {
				 
		
		
				$array = $form->getData();
                if(!empty($array['alias'])){
                    if($array['alias'] != $row->alias){
                        $array['alias'] = $articleTable->getValidAlias(safeUrl($array['alias']));
                    }

                }
                else{
                    $array['alias'] = $articleTable->getValidAlias(safeUrl($data['article_name']));
                }
                $array['alias'] = strtolower($array['alias']);
				$array[$articleTable->getPrimary()]=$id;
				$articleTable->saveRecord($array);
				//    $this->flashmessenger()->addMessage(__('Changes Saved!'));
				$output['message'] = __('Changes Saved!');
		
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
		$output['pageTitle']= __('Edit Article');
		$output['row']= $row;
		$output['action']='edit';
		
		$viewModel = new ViewModel($output);
		$viewModel->setTemplate('admin/articles/add');
		return $viewModel ;
		
	}
	
	

	public function deleteAction()
	{
		$table = new ArticlesTable($this->getServiceLocator());
		$id = $this->params('id');
		$table->deleteRecord($id);
		$this->flashmessenger()->addMessage(__('Record deleted'));
		$this->redirect()->toRoute('admin/default',array('controller'=>'articles','action'=>'index'));
	}
	
}