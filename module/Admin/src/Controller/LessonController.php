<?php

namespace Admin\Controller;

use Application\Controller\AbstractController;
use Application\Entity\Lesson;
use Application\Entity\LessonToLessonGroup;
use Application\Entity\SessionLesson;
use Application\Form\LessonForm;
use Application\Model\LectureTable;
use Application\Model\LessonFileTable;
use Application\Model\LessonGroupTable;
use Application\Model\LessonTable;
use Application\Model\LessonToLessonGroupTable;
use Application\Model\SessionLessonTable;
use Intermatics\BaseForm;
use Intermatics\HelperTrait;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;  
use Application\Form\LessonFilter;
use Intermatics\UtilityFunctions;
/**
 * NewsController
 *
 * @author
 *
 * @version
 *
 */
if(!defined('DIR_MER_IMAGE')) define('DIR_MER_IMAGE', 'public/');
class LessonController extends AbstractController {
 use HelperTrait;

    public function indexAction() {
        // TODO Auto-generated NewssController::indexAction() default action
        $table = new LessonTable($this->getServiceLocator());

        $filter = $this->params()->fromQuery('filter', null);


        if (empty($filter)) {
            $filter=null;
        }

        $group = $this->params()->fromQuery('group', null);
        if (empty($group)) {
            $group=null;
        }

        $sort = $this->params()->fromQuery('sort', null);
        if (empty($sort)) {
            $sort=null;
        }


        $text = new Text('filter');
        $text->setAttribute('class','form-control');
        $text->setAttribute('placeholder',__('Filter by class name'));
        $text->setValue($filter);

        $select = new Select('group');
        $select->setAttribute('class','form-control select2');
        $select->setEmptyOption('--'.__('Select Class Group').'--');

        $sortSelect = new Select('sort');
        $sortSelect->setAttribute('class','form-control');
        $sortSelect->setValueOptions([
            'recent'=>__('Recently Added'),
            'asc'=>__('Alphabetical (Ascending)'),
            'desc'=>__('Alphabetical (Descending)'),
            'sortOrder'=>__('Sort Order'),
            'session'=>__('Physical Location'),
            'online'=>__('Online')
        ]);
        $sortSelect->setEmptyOption('--'.__('Sort').'--');
        $sortSelect->setValue($sort);

        $groupTable = new LessonGroupTable($this->getServiceLocator());
        $groupRowset = $groupTable->getLimitedRecords(1000);
        $options =[];

        foreach($groupRowset as $row){
            $options[$row->lesson_group_id] = $row->group_name;
        }
        $select->setValueOptions($options);
        $select->setValue($group);



        $paginator = $table->getLessons(true,$filter,$group,$sort);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        return new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Classes'),
            'lectureTable'=> new LectureTable($this->getServiceLocator()),
            'filter'=>$filter,
            'group'=>$group,
            'text'=>$text,
            'select'=>$select,
            'sortSelect'=>$sortSelect,
            'sort'=>$sort
        ));


    }

    public function addAction()
    {
        $output = array();
        $lessonTable = new LessonTable($this->getServiceLocator());
        $lessonGroupTable = new LessonToLessonGroupTable($this->getServiceLocator());
        $form = new LessonForm(null,$this->getServiceLocator());
        $filter = new LessonFilter();


        if ($this->request->isPost()) {

            $form->setInputFilter($filter);
            $data = $this->request->getPost();
            $form->setData($data);
            if ($form->isValid()) {

                $array = $form->getData();
                unset($array['lesson_group_id[]']);
                $array[$lessonTable->getPrimary()]=0;
                $id = $lessonTable->saveRecord($array);

                //now put the records in
                if(isset($data['lesson_group_id'])){
                    foreach($data['lesson_group_id'] as $value){
                        $groupId = $value[0];
                        $lessonGroupTable->addRecord([
                            'lesson_id'=>$id,
                            'lesson_group_id'=>$groupId
                        ]);
                    }
                }


                $output['message'] = __('Record Added!');
                $form = new LessonForm(null,$this->getServiceLocator());
                $this->flashMessenger()->addMessage('Class Added!');

                //now check if session id is present
                if(isset($_GET['sessionId'])){
                    $sessionId = $_GET['sessionId'];
                    $sessionLessonTable = new SessionLessonTable();

                    $last = SessionLesson::where('session_id',$sessionId)->orderBy('sort_order','desc')->first();
                    if($last){
                        $sortOrder = $last->sort_order + 1;
                    }
                    else{
                        $sortOrder = 1;
                    }

                    $sessionLessonTable->addRecord([
                        'session_id'=>$sessionId,
                        'lesson_id'=>$id,
                        'sort_order'=>$sortOrder
                    ]);
                    return $this->goBack();
                }



                if($array['lesson_type']=='c'){
                    $this->redirect()->toRoute('admin/default',array('controller'=>'lecture','action'=>'index','id'=>$id));

                }
                else{
                    $this->redirect()->toRoute('admin/default',array('controller'=>'lesson','action'=>'index'));

                }

            }
            else{

                if(isset($data['lesson_group_id'])){
                    foreach($data['lesson_group_id'] as $value){
                        $array['lesson_group_id[]'][] = $value[0];
                    }
                }

                $form->setData($data);

                $output['message'] = __('save-failed-msg');
                if ($data['picture']) {
                    $output['display_image']= resizeImage($data['picture'], 100, 100,$this->getBaseUrl());
                }
                else{
                    $output['no_image']= resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
                    $output['display_image']= resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());

                }
            }

        }
        else{
            $output['no_image']= resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
            $output['display_image']= resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
        }
        $output['form'] = $form;
        $output['pageTitle']= __('Add Class');
        $output['action']='add';
        $output['id']=null;
        return new ViewModel($output);
    }

    public function editAction(){
        $output = array();
        $lessonTable = new LessonTable($this->getServiceLocator());
        $lessonGroupTable = new LessonToLessonGroupTable($this->getServiceLocator());
        $form = new LessonForm(null,$this->getServiceLocator());
        $filter = new LessonFilter();
        $id = $this->params('id');

        $row = $lessonTable->getRecord($id);
        if ($this->request->isPost()) {

            $form->setInputFilter($filter);
            $data = $this->request->getPost();

            $form->setData($data);
            if ($form->isValid()) {

                //add groups
                $array = $form->getData();
                $lessonGroupTable->clearLessonRecords($id);

                //now put the records in
                foreach($data['lesson_group_id'] as $value){
                    $groupId = $value[0];
                    $lessonGroupTable->addRecord([
                       'lesson_id'=>$id,
                        'lesson_group_id'=>$groupId
                    ]);
                }


                $array[$lessonTable->getPrimary()]=$id;
                unset($array['lesson_group_id[]']);
                $lessonTable->saveRecord($array);
                //    $this->flashmessenger()->addMessage(__('Changes Saved!'));
                $output['message'] = __('Changes Saved!');
                $row = $lessonTable->getRecord($id);
                $this->flashMessenger()->addMessage(__('Changes Saved!'));
                $this->redirect()->toRoute('admin/default',array('controller'=>'lesson','action'=>'index'));

            }
            else{
                foreach($data['lesson_group_id'] as $value){
                    $array['lesson_group_id[]'][] = $value[0];
                }
                $form->setData($array);
                $output['message'] = __('save-failed-msg');
            }

        }
        else {

            $data = UtilityFunctions::getObjectProperties($row);

            //get group records
            $groups = [];
            $rowset = $lessonGroupTable->getLessonRecords($id);
            foreach($rowset as $groupRow){
                $data['lesson_group_id[]'][] = $groupRow->lesson_group_id;
            }


            $form->setData($data);



        }

        if ($row->picture && file_exists(DIR_MER_IMAGE . $row->picture) && is_file(DIR_MER_IMAGE . $row->picture)) {
            $output['display_image'] = resizeImage($row->picture, 100, 100,$this->getBaseUrl());
        } else {
            $output['display_image'] = resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
        }


        $output['no_image']= $this->getBaseUrl().'/'.resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
        $output['form'] = $form;
        $output['id'] = $id;
        $output['pageTitle']= __('Edit Class');
        $output['row']= $row;
        $output['action']='edit';

        $viewModel = new ViewModel($output);
        $viewModel->setTemplate('admin/lesson/add');
        return $viewModel ;

    }

    public function getBaseUrl()
    {
        $event = $this->getEvent();
        $request = $event->getRequest();
        $router = $event->getRouter();
        $uri = $router->getRequestUri();
        $baseUrl = sprintf('%s://%s%s', $uri->getScheme(), $uri->getHost(), $request->getBaseUrl());
        return $baseUrl;
    }


    public function deleteAction()
    {
        $table = new LessonTable($this->getServiceLocator());
        $id = $this->params('id');
        try{
            $table->deleteRecord($id);
            $this->flashmessenger()->addMessage(__('Record deleted'));
        }
        catch(\Exception $ex){
            $this->deleteError();
        }
        return $this->goBack();

       // $this->redirect()->toRoute('admin/default',array('controller'=>'lesson','action'=>'index'));
    }

    /**
     * Show groups
     */
    public function groupsAction(){

        $table = new LessonGroupTable($this->getServiceLocator());

        $paginator = $table->getPaginatedRecords(true);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        return new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Class Groups'),
        ));


    }

    private function getGroupForm(){
        $form = new BaseForm();
        $form->createText('group_name','Group Name',true);
        $form->createTextArea('description','Description');
        $form->createText('sort_order','Sort Order',false,'form-control number',null,__('Digits Only'));
        return $form;
    }

    private function getGroupFilter(){
        $filter = new InputFilter();
        $filter->add(array(
            'name'=>'group_name',
            'required'=>true,
            'validators'=>array(
                array(
                    'name'=>'NotEmpty'
                )
            )
        ));

        $filter->add(array(
            'name'=>'description',
            'required'=>false,

        ));


        $filter->add(array(
            'name'=>'sort_order',
            'required'=>false,
            'validators'=>array(
                array(
                    'name'=>'Digits'
                )
            )
        ));


        return $filter;
    }

    /**
     * Add a new group
     */
    public function addgroupAction(){

            $output = array();
            $table = new LessonGroupTable($this->getServiceLocator());
            $form = $this->getGroupForm();
            $filter = $this->getGroupFilter();


            if ($this->request->isPost()) {

                $form->setInputFilter($filter);
                $data = $this->request->getPost();
                $form->setData($data);
                if ($form->isValid()) {

                    $array = $form->getData();


                    $array[$table->getPrimary()]=0;
                    $table->saveRecord($array);
                    //    $this->flashmessenger()->addMessage(__('Changes Saved!'));
                    $output['message'] = __('Record Added!');
                    $this->flashMessenger()->addMessage('Group Created Successfully');
                    $this->redirect()->toRoute('admin/default',array('controller'=>'lesson','action'=>'groups'));

                }
                else{
                    $output['message'] = __('save-failed-msg');
                }

            }


            $output['form'] = $form;
            $output['pageTitle']= __('Add Class Group');
            $output['action']='addgroup';
            $output['id']=null;
            return new ViewModel($output);


    }

    public function editgroupAction(){
        $output = array();
        $table = new LessonGroupTable($this->getServiceLocator());

        $filter = $this->getGroupFilter();
        $id = $this->params('id');
        $form = $this->getGroupForm();

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
                $this->flashMessenger()->addMessage(__('Changes Saved!'));

                $this->redirect()->toRoute('admin/default',array('controller'=>'lesson','action'=>'groups'));

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
        $output['pageTitle']= __('Edit Class Group');
        $output['row']= $row;
        $output['action']='editgroup';

        $viewModel = new ViewModel($output);
        $viewModel->setTemplate('admin/lesson/addgroup');
        return $viewModel ;
    }

    public function deletegroupAction(){
        $table = new LessonGroupTable($this->getServiceLocator());
        $id = $this->params('id');

        try{
            $table->deleteRecord($id);
            $this->flashmessenger()->addMessage(__('Record deleted'));
        }
        catch(\Exception $ex){
            $this->deleteError();
        }

        $this->redirect()->toRoute('admin/default',array('controller'=>'lesson','action'=>'groups'));

    }

    public function filesAction(){

        $id = $this->params('id');
        $table = new LessonFileTable($this->getServiceLocator());
        $lessonTable = new LessonTable($this->getServiceLocator());
        $lessonRow = $lessonTable->getRecord($id);
        $rowset = $table->getDownloadRecords($id);
        $viewModel = new ViewModel(['rowset'=>$rowset,
        'pageTitle'=>__('Class Downloads').': '.$lessonRow->lesson_name,
            'id'=>$id
        ]);
        
        return $viewModel;
    }

    public function addfileAction(){
        $path = $this->request->getQuery('path');
        $id = $this->params('id');

        $downloadFileTable = new LessonFileTable($this->getServiceLocator());
        $path = str_ireplace('usermedia/','',$path);
        if(!$downloadFileTable->fileExists($id,$path)){
            $downloadFileTable->addRecord([
                'lesson_id'=>$id,
                'path'=>$path,
                'created_on'=>time(),
                'status'=>1
            ]);
        }


        $filesViewModel = $this->forward()->dispatch('Admin\Controller\Lesson',['action'=>'files','id'=>$id]);
        $filesViewModel->setTerminal(true);
        return $filesViewModel;
    }

    public function removefileAction(){

        $id = $this->params('id');
        $downloadFileTable = new LessonFileTable($this->getServiceLocator());
        $row = $downloadFileTable->getRecord($id);
        $downloadId = $row->lesson_id;

        $downloadFileTable->deleteRecord($id);
        $filesViewModel = $this->forward()->dispatch('Admin\Controller\Lesson',['action'=>'files','id'=>$downloadId]);
        $filesViewModel->setTerminal(true);
        return $filesViewModel;
    }


    public function downloadAction(){
        set_time_limit(86400);
        $id = $this->params('id');
        $table = new LessonFileTable($this->getServiceLocator());
        $row = $table->getRecord($id);
        $path = 'public/usermedia/'.$row->path;



        header('Content-type: '.getFileMimeType($path));

// It will be called downloaded.pdf
        header('Content-Disposition: attachment; filename="'.basename($path).'"');

// The PDF source is in original.pdf
        readfile($path);
        exit();
    }


    public function duplicateAction(){

        $id = $this->params('id');
        $oldLesson= Lesson::find($id);

        $lesson= $oldLesson->replicate();
        $lesson->save();

        //get all lectures
        foreach($oldLesson->lectures as $oldLecture){
            $data = $oldLecture->toArray();
            unset($data['lecture_id']);
            $lecture= $lesson->lectures()->create($data);
            //now save lecture pages
            foreach($oldLecture->lecturePages as $oldLecturePage){
                $data = $oldLecturePage->toArray();
                unset($data['lecture_page_id']);
                $lecture->lecturePages()->create($data);
            }

            foreach($oldLecture->lectureFiles as $oldLectureFile){
                $data = $oldLectureFile->toArray();
                unset($data['lecture_file_id']);
                $lecture->lectureFiles()->create($data);
            }

        }

        $lessonToLessonGroups= LessonToLessonGroup::where('lesson_id',$id)->get();
        foreach($lessonToLessonGroups as $record){
            LessonToLessonGroup::create([
               'lesson_group_id'=>$record->lesson_group_id,
                'lesson_id'=>$lesson->lesson_id
            ]);
        }

        $this->flashMessenger()->addMessage(__('record-duplicated'));
        return $this->goBack();


    }


}