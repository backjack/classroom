<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 7/27/2017
 * Time: 2:18 PM
 */

namespace Admin\Controller;


use Application\Controller\AbstractController;
use Application\Form\DownloadFilter;
use Application\Form\DownloadForm;
use Application\Model\DownloadFileTable;
use Application\Model\DownloadSessionTable;
use Application\Model\DownloadTable;
use Application\Model\SessionInstructorTable;
use Intermatics\UtilityFunctions;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DownloadController extends AbstractController {

    public function indexAction(){
        $table = new DownloadTable($this->getServiceLocator());
        $downloadFileTable = new DownloadFileTable($this->getServiceLocator());

        $paginator = $table->getPaginatedRecords(true);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        return new ViewModel(array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Downloads'),
            'downloadTable'=>$table,
            'fileTable'=>$downloadFileTable
        ));
    }

    public function addAction()
    {
        $output = array();
        $downloadTable = new DownloadTable($this->getServiceLocator());
        $form = new DownloadForm(null,$this->getServiceLocator());
        $filter = new DownloadFilter();

        if ($this->request->isPost()) {

            $form->setInputFilter($filter);
            $data = $this->request->getPost();
            $form->setData($data);
            if ($form->isValid()) {

                $array = $form->getData();
                $array[$downloadTable->getPrimary()]=0;
                $array['created_on']=time();
                $id= $downloadTable->saveRecord($array);
                $this->flashmessenger()->addMessage(__('download-created!'));

                $this->redirect()->toRoute('admin/default',['controller'=>'download','action'=>'edit','id'=>$id]);


            }
            else{
                $output['message'] = __('save-failed-msg');

            }

        }

        $output['form'] = $form;
        $output['pageTitle']= __('Add Download');
        $output['action']='add';
        $output['id']=null;
        return new ViewModel($output);
    }


    public function editAction(){
        $output = array();
        $table = new DownloadTable($this->getServiceLocator());

        $filter = new DownloadFilter();
        $id = $this->params('id');
        $form = new DownloadForm(null,$this->getServiceLocator());

        $row = $table->getRecord($id);
        if ($this->request->isPost()) {

            $form->setInputFilter($filter);
            $data = $this->request->getPost();
            $form->setData($data);
            if ($form->isValid()) {



                $array = $form->getData();

                $array[$table->getPrimary()]=$id;
                $table->saveRecord($array);
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
        $output['pageTitle']= __('Edit Download');
        $output['row']= $row;
        $output['action']='edit';


        $filesViewModel = $this->forward()->dispatch('Admin\Controller\Download',['action'=>'files','id'=>$id]);
        $viewRender = $this->getServiceLocator()->get('ViewRenderer');
        $html = $viewRender->render($filesViewModel);
        $output['files'] = $html;

        $sessionsViewModel = $this->forward()->dispatch('Admin\Controller\Download',['action'=>'sessions','id'=>$id]);
        $viewRender = $this->getServiceLocator()->get('ViewRenderer');
        $html = $viewRender->render($sessionsViewModel);
        $output['sessions'] = $html;


        $viewModel = new ViewModel($output);



        return $viewModel ;

    }



    public function filesAction(){

        $id = $this->params('id');
        $table = new DownloadFileTable($this->getServiceLocator());
        $rowset = $table->getDownloadRecords($id);
        $viewModel = new ViewModel(['rowset'=>$rowset]);
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    public function sessionsAction(){

        $id = $this->params('id');
        $table = new DownloadSessionTable($this->getServiceLocator());
        $rowset = $table->getDownloadRecords($id);
        $viewModel = new ViewModel(['rowset'=>$rowset]);
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    public function addfileAction(){
        $path = $this->request->getQuery('path');
        $id = $this->params('id');

        $downloadFileTable = new DownloadFileTable($this->getServiceLocator());
        $path = str_ireplace('usermedia/','',$path);
        if(!$downloadFileTable->fileExists($id,$path)){
            $downloadFileTable->addRecord([
                'download_id'=>$id,
                'path'=>$path,
                'created_on'=>time(),
                'status'=>1
            ]);
        }


        $filesViewModel = $this->forward()->dispatch('Admin\Controller\Download',['action'=>'files','id'=>$id]);
        return $filesViewModel;
    }

    public function removefileAction(){

        $id = $this->params('id');
        $downloadFileTable = new DownloadFileTable($this->getServiceLocator());
        $row = $downloadFileTable->getRecord($id);
        $downloadId = $row->download_id;

        $downloadFileTable->deleteRecord($id);
        $filesViewModel = $this->forward()->dispatch('Admin\Controller\Download',['action'=>'files','id'=>$downloadId]);
        return $filesViewModel;
    }

    public function addsessionAction(){

        $id = $this->params('id');
        $downloadSessionTable = new DownloadSessionTable($this->getServiceLocator());
       $count = 0;
        if($this->request->isPost()){
            $data = $this->request->getPost();

            foreach($data as $key=>$value){
                if(preg_match('#session_#',$key) && !$downloadSessionTable->sessionExists($id,$value)){
                    $downloadSessionTable->addRecord([
                        'download_id'=>$id,
                        'session_id'=>$value
                    ]);
                    $count++;

                }
            }
            $this->flashMessenger()->addMessage($count.' '.__('added-to-download-msg'));
        }

        $this->redirect()->toRoute('admin/default',['controller'=>'download','action'=>'edit','id'=>$id]);
    }

    public function removesessionAction(){

        $id = $this->params('id');
        $downloadSessionTable = new DownloadSessionTable($this->getServiceLocator());
        $row = $downloadSessionTable->getRecord($id);
        $downloadId = $row->download_id;

        $downloadSessionTable->deleteRecord($id);
        $filesViewModel = $this->forward()->dispatch('Admin\Controller\Download',['action'=>'sessions','id'=>$downloadId]);
        return $filesViewModel;
    }


    public function deleteAction(){
        $id= $this->params('id');
        $table = new DownloadTable($this->getServiceLocator());
        $table->deleteRecord($id);
        $this->flashMessenger()->addMessage(__('Record deleted'));
        $this->redirect()->toRoute('admin/default',['controller'=>'download','action'=>'index']);

    }


    public function duplicateAction(){

        $id = $this->params('id');

        //get tables
        $downloadTable = new DownloadTable($this->getServiceLocator());
        $downloadFileTable = new DownloadFileTable($this->getServiceLocator());
        $downloadSessionTable = new DownloadSessionTable($this->getServiceLocator());

        //now get session records
        $downloadRow = $downloadTable->getRecord($id);
        $downloadFileRowset = $downloadFileTable->getDownloadRecords($id);
        $downloadSessionRowset = $downloadSessionTable->getDownloadRecords($id);

        //create row
        $downloadArray= UtilityFunctions::getObjectProperties($downloadRow);
        unset($downloadArray['download_id']);
        $newId = $downloadTable->addRecord($downloadArray);

        //now get lessons
        foreach($downloadFileRowset as $row){
            $data = UtilityFunctions::getObjectProperties($row);

            $data['download_id']=$newId;
            $downloadFileTable->addRecord($data);
        }

        //get instructors
        foreach($downloadSessionRowset as $row){
            $data = UtilityFunctions::getObjectProperties($row);

            $data['download_id']=$newId;
            $downloadSessionTable->addRecord($data);
        }

        $this->flashMessenger()->addMessage(__('Download duplicated successfully'));
        $this->redirect()->toRoute('admin/default',array('controller'=>'download','action'=>'index'));


    }

    public function browsesessionsAction(){
        $id = $this->params('id');
        $viewModel = $this->forward()->dispatch('Admin\Controller\Student',['action'=>'sessions']);
        $viewModel->setTemplate('admin/download/browsesessions');
        $viewModel->setTerminal(true);
        $viewModel->setVariable('id',$id);

        $sessionInstructorTable = new SessionInstructorTable($this->getServiceLocator());
        $assigned = $sessionInstructorTable->getAccountRecords(ADMIN_ID);
        $viewModel->setVariable('assigned',$assigned);
        return $viewModel;
    }

    public function downloadAction(){
        set_time_limit(86400);
        $id = $this->params('id');
        $table = new DownloadFileTable($this->getServiceLocator());
        $row = $table->getRecord($id);
        $path = 'public/usermedia/'.$row->path;



        header('Content-type: '.getFileMimeType($path));

// It will be called downloaded.pdf
        header('Content-Disposition: attachment; filename="'.basename($path).'"');

// The PDF source is in original.pdf
        readfile($path);
        exit();
    }







}