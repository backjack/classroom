<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 2/22/2018
 * Time: 2:12 PM
 */

namespace Admin\Controller;


use Application\Controller\AbstractController;
use Application\Form\TemplateOptionForm;
use Application\Model\TemplateOptionTable;
use Application\Model\TemplateTable;
use Intermatics\HelperTrait;
use Zend\Mvc\Controller\AbstractActionController;

class TemplateController extends AbstractController {

    use HelperTrait;

    public function indexAction()
    {
        //get templates
        $table = new TemplateTable($this->getServiceLocator());
        $rowset = $table->getRecords();
        $template = $table->getActiveTemplate();


        return ['pageTitle'=>__('Themes'),'rowset'=>$rowset,'template'=>$template];
    }


    public function customizeAction(){

        $output = [];
        $id = $this->params('id');
        $templateTable = new TemplateTable($this->getServiceLocator());
        $templateOptionTable = new TemplateOptionTable($this->getServiceLocator());

        //get option groups
        $groups = $templateOptionTable->getGroups($id);

        $templateOptionForm = new TemplateOptionForm(null,$this->getServiceLocator(),$id);
        $rowset = $templateOptionTable->getTemplateRecords($id);
        $rowset->buffer();

        if($this->request->isPost())
        {

            unset($_SESSION['style']);
            $formData = $this->request->getPost();
            foreach($rowset as $row){
                $templateOptionTable->saveOption($id,$row->key,$formData[$row->key]);
            }
            $output['message']=__('Changes Saved!');
            $templateOptionForm->setData($formData);


        }
        else{
            $data = [];
            foreach($rowset as $row){
                $data[$row->key] = $row->value;
            }
            $templateOptionForm->setData($data);

          }
        $groups->buffer();
        $output['groups'] = $groups;
        $output['options'] = $rowset;
        $output['form']=$templateOptionForm;
        $output['pageTitle'] = __('Customize Theme').': '.$templateTable->getRecord($id)->name;
        $output['table'] = $templateOptionTable;
        $output['id'] = $id;
        $output['no_image']= resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
        $output['display_image']= resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());

        return $output;


    }


    public function installAction(){
        $id = $this->params('id');

        $templateTable = new TemplateTable($this->getServiceLocator());
        $templateTable->tableGateway->update(['active'=>0]);
        $templateTable->update(['active'=>1],$id);
        $this->flashMessenger()->addMessage(__('Theme Changed'));
        return $this->redirect()->toRoute('admin/default',['controller'=>'template','action'=>'customize','id'=>$id]);


    }




}