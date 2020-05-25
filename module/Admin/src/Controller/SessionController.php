<?php

namespace Admin\Controller;

use Application\Controller\AbstractController;
use Application\Entity\Session;
use Application\Entity\SessionLesson;
use Application\Entity\Student;
use Application\Form\CourseFilter;
use Application\Form\CourseForm;
use Application\Form\LessonFilter;
use Application\Form\LessonForm;
use Application\Form\SessionForm;
use Application\Model\AttendanceTable;
use Application\Model\LectureTable;
use Application\Model\LessonGroupTable;
use Application\Model\LessonToLessonGroupTable;
use Application\Model\SessionCategoryTable;
use Application\Model\LessonTable;
use Application\Model\LessonToSessionCategoryTable;
use Application\Model\SessionInstructorTable;
use Application\Model\SessionLessonTable;
use Application\Model\SessionTable;
use Application\Model\SessionTestTable;
use Application\Model\SessionToSessionCategoryTable;
use Application\Model\StudentSessionTable;
use Application\Model\StudentTable;
use Application\Model\StudentTestTable;
use Application\Model\TestTable;
use Intermatics\BaseForm;
use Intermatics\HelperTrait;
use Intermatics\SmsGateway;
use Intermatics\UtilityFunctions;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
define('DIR_MER_IMAGE', 'public/');
class SessionController extends AbstractController {

    use HelperTrait;

        public function sessiontypeAction(){

            $output = [];
            $id = $this->params('id');
            $sessionTable = new SessionTable($this->getServiceLocator());

            $row = $sessionTable->getRecord($id);
            $select = new Select('session_type');
            $select->setAttribute('class','form-control');
            $select->setValueOptions([
                's'=>__('Training Session'),
                'b'=>__('training-online')
            ]);
            $select->setValue($row->session_type);
            if($this->request->isPost()){
                $type = $this->request->getPost('session_type');
                $sessionTable->update(['session_type'=>$type],$id);
                $this->flashMessenger()->addMessage(__('Changes Saved!'));
                $this->redirect()->toRoute('admin/default',['controller'=>'student','action'=>'sessions']);
            }

            $output['select'] = $select;
            $output['id'] = $id;
            $viewModel = new ViewModel($output);
            $viewModel->setTerminal(true);
            return $viewModel;
        }

    public function addcourseAction(){
        $table = new SessionTable($this->getServiceLocator());
        $output = array();
        $output['id']=0;

        $filter = new CourseFilter();
        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());
        $lessonTable = new LessonTable($this->getServiceLocator());
        $sessionCategoryTable = new SessionToSessionCategoryTable($this->getServiceLocator());
        $sessionInstructorTable = new SessionInstructorTable($this->getServiceLocator());

        $dbType = 'c';
        $type= 'c';
        $form = new CourseForm(null,$this->getServiceLocator(),$dbType);
        $lessons = $lessonTable->getLimitedLessonRecords($dbType,5000);
        $total = $lessons->count();
        $output['lessons'] = $lessons;
       /* if(empty($total)){
            $link = $this->url()->fromRoute('admin/default',array('controller'=>'lesson','action'=>'add'));
            $message = "You have not created any online classes yet. Please click <a style=\"text-decoration:underline\" href=\"$link\">here</a> or use the 'Create Class' button below to add your first class before you create a course.";
            $output['message']= $message;

        }*/

        if($this->request->isPost()){
            $formData = $this->request->getPost();
            $form->setInputFilter($filter);
            $form->setData($formData);
            if($form->isValid()){
                $data = $form->getData();

                if(!empty($data['session_date'])){
                   $date = strtotime($data['session_date']);
                }
                else{
                    $date = '';
                }

                if(!empty($data['session_end_date'])){
                     $endDate = strtotime($data['session_end_date']);
                }
                else{
                    $endDate=null;
                }

                if(!empty($data['enrollment_closes'])){
                     $closesOn = strtotime($data['enrollment_closes']);
                }
                else{
                    $closesOn=null;
                }


                $dbData = array(
                    'session_name'=>$data['session_name'],
                    'session_status'=>$data['session_status'],
                    'payment_required'=>$data['payment_required'],
                    'amount'=>$data['amount'],
                    'description'=>$data['description'],
                    'session_type'=>$type,
                    'picture'=>$data['picture'],
                    'session_date'=>time(),
                    'enable_discussion'=>$data['enable_discussion'],
                    'session_date'=>$date,
                    'session_end_date'=>$endDate,
                    'enrollment_closes'=>$closesOn,
                    'effort'=>$data['effort'],
                    'length'=>$data['length'],
                    'short_description'=>$data['short_description'],
                    'introduction'=>$data['introduction'],
                    'enable_forum'=>$data['enable_forum'],
                );



                $sessionId= $table->addRecord($dbData);

                if(isset($formData['session_category_id'])){
                    //now put the records in
                    foreach($formData['session_category_id'] as $value){
                        $groupId = $value[0];
                        $sessionCategoryTable->addRecord([
                            'session_id'=>$sessionId,
                            'session_category_id'=>$groupId
                        ]);
                    }
                }


                if(isset($formData['session_instructor_id'])){
                    foreach($formData['session_instructor_id'] as $value){
                        $groupId = $value[0];
                        $sessionInstructorTable->addRecord([
                            'session_id'=>$sessionId,
                            'account_id'=>$groupId
                        ]);
                    }
                }



             //   $rowset = $lessonTable->getLimitedRecords(5000);
              /*  foreach($lessons as $row){
                    if(!empty($data['lesson_'.$row->lesson_id])){

                        $ldata = array(
                            'lesson_id'=>$row->lesson_id,
                            'session_id'=>$sessionId,
                            'sort_order'=>$data['sort_order_'.$row->lesson_id],
                        );

                        if(!empty($data['lesson_date_'.$row->lesson_id])){
                            $ldata['lesson_date'] = strtotime($data['lesson_date_'.$row->lesson_id]);
                        }

                        $sessionLessonTable->addRecord($ldata);

                    }


                }

                $sessionLessonTable->arrangeSortOrders($sessionId);*/
                $this->flashMessenger()->addMessage(__('course-add-msg'));
                $this->redirect()->toRoute('admin/default',['controller'=>'session','action'=>'courseclasses','id'=>$sessionId]);


            }
            else{

                $output['message'] = $this->getFormErrors($form);
                $array=[];
                if(isset($formData['session_category_id'])){
                    foreach($formData['session_category_id'] as $value){
                        $array['session_category_id[]'][] = $value[0];
                    }
                }

                if(isset($formData['session_instructor_id'])){

                    foreach($formData['session_instructor_id'] as $value){

                        $array['session_instructor_id[]'][] = $value[0];;
                    }
                }


                $form->setData($array);

              /*  if(!$this->lessonSelected($formData)){
                    $output['message'] .= 'Ensure that you select at least one class';
                }*/

                if ($formData['picture']) {
                    $output['display_image']= resizeImage($formData['picture'], 100, 100,$this->getBaseUrl());
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
        $output['action'] = 'add';
        $output['type'] = $type;
        $output['pageTitle'] = __('Add Online Course');
        $output['lessonGroupTable'] = new LessonToLessonGroupTable($this->getServiceLocator());
        return $output;
    }

    public function editcourseAction(){
        $table = new SessionTable($this->getServiceLocator());
        $output = array();
        $output['pageTitle'] = __('Edit Online Course');
        $output['lessonGroupTable'] = new LessonToLessonGroupTable($this->getServiceLocator());
        $id = $this->params('id');
        $sessionRow = $table->getRecord($id);
        $type = $sessionRow->session_type;
        $dbType = 'c';

        $form = new CourseForm(null,$this->getServiceLocator(),$dbType);
        $sessionCategoryTable = new SessionToSessionCategoryTable($this->getServiceLocator());
        $sessionInstructorTable = new SessionInstructorTable($this->getServiceLocator());


        $filter = new CourseFilter();
        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());
        $lessonTable = new LessonTable($this->getServiceLocator());
        $lessons = $lessonTable->getLimitedLessonRecords($dbType,5000);

        $total = $lessons->count();
        $output['lessons'] = $lessons;
        if(empty($total)){
            $link = $this->url()->fromRoute('admin/default',array('controller'=>'lesson','action'=>'add'));
            $message = __("no-classes-msg",['link'=>$link]);
            $output['message']= $message;

        }

        if($this->request->isPost()){
            $formData = $this->request->getPost();
            $form->setInputFilter($filter);
            $form->setData($formData);

            if($form->isValid()){

                $data = $form->getData();


                if(!empty($data['session_date'])){
                    $date = strtotime($data['session_date']);
                }
                else{
                    $date = '';
                }

                if(!empty($data['session_end_date'])){
                    $endDate = strtotime($data['session_end_date']);
                }
                else{
                    $endDate='';
                }

                if(!empty($data['enrollment_closes'])){
                    $closesOn = strtotime($data['enrollment_closes']);
                }
                else{
                    $closesOn='';
                }

                $dbData = array(
                    'session_name'=>$data['session_name'],
                    'session_status'=>$data['session_status'],
                    'payment_required'=>$data['payment_required'],
                    'amount'=>$data['amount'],
                    'description'=>$data['description'],
                    'picture'=>$data['picture'],
                    'enable_discussion'=>$data['enable_discussion'],
                    'enable_chat'=>$data['enable_chat'],
                    'enforce_order'=>$data['enforce_order'],
                    'session_date'=>$date,
                    'session_end_date'=>$endDate,
                    'enrollment_closes'=>$closesOn,
                    'effort'=>$data['effort'],
                    'length'=>$data['length'],
                    'short_description'=>$data['short_description'],
                    'introduction'=>$data['introduction'],
                    'enable_forum'=>$data['enable_forum'],

                );


                $table->update($dbData,$id);

                $sessionCategoryTable->clearSessionRecords($id);
                if(isset($formData['session_category_id'])){
                    foreach($formData['session_category_id'] as $value){
                        $groupId = $value[0];
                        $sessionCategoryTable->addRecord([
                            'session_id'=>$id,
                            'session_category_id'=>$groupId
                        ]);
                    }
                }


                $sessionInstructorTable->clearSessionRecords($id);
                if(isset($formData['session_instructor_id'])){
                    foreach($formData['session_instructor_id'] as $value){
                        $groupId = $value[0];
                        $sessionInstructorTable->addRecord([
                            'session_id'=>$id,
                            'account_id'=>$groupId
                        ]);
                    }
                }






                $this->flashMessenger()->addMessage(__('Changes Saved!'));
                $this->redirect()->toRoute('admin/default',array('controller'=>'student','action'=>'sessions'));
            }
            else{

                $output['message'] = $this->getFormErrors($form);
                $array=[];
                if(isset($formData['session_category_id'])){
                    foreach($formData['session_category_id'] as $value){
                        $array['session_category_id[]'][] = $value[0];
                    }
                }

                if(isset($formData['session_instructor_id'])){
                    foreach($formData['session_instructor_id'] as $value){

                        $array['session_instructor_id[]'][] = $value[0];;
                    }
                }

                $form->setData($array);

            }
        }
        else{
            $row = $table->getRecord($id);
            $data = UtilityFunctions::getObjectProperties($row);
            if(!empty($data['session_date']))
            $data['session_date'] = date('Y-m-d',$row->session_date);

            if(!empty($data['session_end_date']))
            $data['session_end_date'] = date('Y-m-d',$row->session_end_date);

            if(!empty($data['enrollment_closes']))
            $data['enrollment_closes'] = date('Y-m-d',$row->enrollment_closes);

            $groups = [];

            $rowset = $sessionCategoryTable->getSessionRecords($id);
            foreach($rowset as $groupRow){
                $data['session_category_id[]'][] = $groupRow->session_category_id;
            }

            $rowset = $sessionInstructorTable->getSessionRecords($id);
            foreach($rowset as $groupRow){
                $data['session_instructor_id[]'][] = $groupRow->account_id;
            }


            //get session lessons
            $rowset = $sessionLessonTable->getSessionRecords($id);
            foreach($rowset as $row){
                $data['lesson_'.$row->lesson_id]=$row->lesson_id;
                if(!empty($row->lesson_date)){
                    $data['lesson_date_'.$row->lesson_id]= date('Y-m-d',$row->lesson_date);
                }

                if(!empty($row->sort_order)){
                    $data['sort_order_'.$row->lesson_id]= $row->sort_order;
                }


            }

            $form->setData($data);
        }

        $row = $table->getRecord($id);
        if ($row->picture && file_exists(DIR_MER_IMAGE . $row->picture) && is_file(DIR_MER_IMAGE . $row->picture)) {
            $output['display_image'] = resizeImage($row->picture, 100, 100,$this->getBaseUrl());
        } else {

            $output['display_image'] = resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
        }


        $output['no_image']= $this->getBaseUrl().'/'.resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());

        $output['form'] = $form;
        $output['action'] = 'edit';
        $output['id'] = $id;
        $output['type'] = $type;
        $viewModel = new ViewModel($output);
        $viewModel->setTemplate('admin/session/addcourse.phtml');
        return $viewModel;
    }
    public function sessionclassesAction(){
        $id= $this->params('id');
        $sessionTable = new SessionTable();
        $sessionLessonTable =  new SessionLessonTable();
        $sessionLessonTable->arrangeSortOrders($id);
        $sessionTable->getRecord($id);
        //get session
        $session = Session::find($id);

        $this->data['session'] = $session;


        $addClassView = $this->forward()->dispatch('Admin\Controller\Lesson',['action'=>'add']);
        $this->data = $this->data + $addClassView->getVariables();

        $this->data['pageTitle'] = __('Manage Classes').': '.$session->session_name;

        return $this->data;
    }


    public function courseclassesAction(){
        $id= $this->params('id');
        $sessionTable = new SessionTable();
        $sessionLessonTable =  new SessionLessonTable();
        $sessionLessonTable->arrangeSortOrders($id);
        $sessionTable->getRecord($id);
        //get session
        $session = Session::find($id);

        $this->data['session'] = $session;


        $addClassView = $this->forward()->dispatch('Admin\Controller\Lesson',['action'=>'add']);
        $this->data = $this->data + $addClassView->getVariables();

        $this->data['pageTitle'] = __('Manage Classes').': '.$session->session_name;

        return $this->data;
    }

    public function browseclassesAction() {
        // TODO Auto-generated NewssController::indexAction() default action
        $sessionId = $this->params('id');
        $table = new LessonTable($this->getServiceLocator());

        $filter = $this->params()->fromQuery('filter', null);


        if (empty($filter)) {
            $filter=null;
        }

        $group = $this->params()->fromQuery('group', null);
        if (empty($group)) {
            $group=null;
        }



        $text = new Text('filter');
        $text->setAttribute('class','form-control');
        $text->setAttribute('placeholder',__('Filter by class name'));
        $text->setValue($filter);

        $select = new Select('group');
        $select->setAttribute('class','form-control select2');
        $select->setEmptyOption('--'.__('Select Class Group').'--');


        $groupTable = new LessonGroupTable($this->getServiceLocator());
        $groupRowset = $groupTable->getLimitedRecords(1000);
        $options =[];

        foreach($groupRowset as $row){
            $options[$row->lesson_group_id] = $row->group_name;
        }
        $select->setValueOptions($options);
        $select->setValue($group);

        $type = $this->request->getQuery('type');
        if(empty($type)){
            $type = 'online';
        }
        else{
            $type = '';
        }

        $paginator = $table->getLessons(true,$filter,$group,$type);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        $model= new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Classes'),
            'lectureTable'=> new LectureTable($this->getServiceLocator()),
            'filter'=>$filter,
            'group'=>$group,
            'text'=>$text,
            'select'=>$select,
            'sessionId'=>$sessionId
        ));
        $model->setTerminal(true);
        return $model;


    }

    public function setclassAction(){
        $id = $this->params('id');
        $sessionTable = new SessionTable();
        $sessionId = $_GET['sessionId'];
        $session = $sessionTable->getRecord($sessionId);


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
        $this->flashMessenger()->addMessage(__('Class Added!'));


        if($session->session_type=='c'){
            return $this->redirect()->toRoute('admin/default',['controller'=>'session','action'=>'courseclasses','id'=>$sessionId]);

        }
        else{
            return $this->redirect()->toRoute('admin/default',['controller'=>'session','action'=>'sessionclasses','id'=>$sessionId]);

        }

    }


    public function reorderAction(){
        if(!empty($_REQUEST['row'])){

            $counter = 1;
            foreach($_REQUEST['row'] as $id){
                $sessionLesson =  SessionLesson::find($id);
                if($sessionLesson){
                    $sessionLesson->sort_order = $counter;
                    $sessionLesson->save();
                    $counter++;
                }

            }

        }
        exit('done');
    }

    public function setdateAction(){
    $id = $this->params('id');
        if($this->request->isPost()){

            $date = $this->request->getPost('date');
            $sessionLesson = SessionLesson::find($id);
            if(empty($date)){
                $dbData = '';
            }
            else{
                $dbData = strtotime($date);
            }

            $sessionLesson->lesson_date= $dbData;
            $sessionLesson->save();
            exit(__('Changes Saved!'));
        }

exit('done');
    }

    public function setstartAction(){
        $id = $this->params('id');
        if($this->request->isPost()){

            $start = $this->request->getPost('start');
            $sessionLesson = SessionLesson::find($id);

            $sessionLesson->lesson_start= $start;
            $sessionLesson->save();
            exit(__('Changes Saved!'));
        }

        exit('done');
    }

    public function setendAction(){
        $id = $this->params('id');
        if($this->request->isPost()){

            $end = $this->request->getPost('end');
            $sessionLesson = SessionLesson::find($id);

            $sessionLesson->lesson_end= $end;
            $sessionLesson->save();
            exit(__('Changes Saved!'));
        }

        exit('done');
    }

    public function setvenueAction(){
        $id = $this->params('id');
        if($this->request->isPost()){

            $venue = $this->request->getPost('venue');
            $sessionLesson = SessionLesson::find($id);

            $sessionLesson->lesson_venue = $venue;
            $sessionLesson->save();
            exit(__('Changes Saved!'));
        }

        exit('done');
    }

    public function lecturesAction(){
        $id = $this->params('id');
        $sessionLesson = SessionLesson::find($id);
        $viewModel = $this->forward()->dispatch('Admin\Controller\Lecture',['action'=>'index','id'=>$sessionLesson->lesson_id]);

        return $viewModel;
    }

    public function deleteclassAction(){
        $id = $this->params('id');
        $item = SessionLesson::find($id);
        $model= new SessionTable();
        $model->getRecord($item->session_id);


        $item->delete();
        $sessionLessonTable= new SessionLessonTable();
        $sessionLessonTable->arrangeSortOrders($item->session_id);
        $this->flashMessenger()->addMessage(_('Class removed'));
        return $this->goBack();
    }

    private function lessonSelected($data){
        $lessonTable = new LessonTable($this->getServiceLocator());
        $rowset = $lessonTable->getRecords();
        $valid=false;
        foreach($rowset as $row){
            if(!empty($data['lesson_'.$row->lesson_id])){
                $valid = true;
            }
        }

        return $valid;
    }



    public function groupsAction(){

        $table = new SessionCategoryTable($this->getServiceLocator());

        $paginator = $table->getPaginatedRecords(true);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        return new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Course Categories'),
        ));


    }

    private function getGroupForm(){
        $form = new BaseForm();
        $form->createText('category_name','Category Name',true);
        $form->createTextArea('description','Description');
        $form->createSelect('status','Status',['1'=>__('Enabled'),'0'=>__('Disabled')],true,false);
        $form->createText('sort_order','Sort Order',false,'form-control number',null,__('Digits Only'));
        return $form;
    }

    private function getGroupFilter(){
        $filter = new InputFilter();
        $filter->add(array(
            'name'=>'category_name',
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
            'name'=>'status',
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
        $table = new SessionCategoryTable($this->getServiceLocator());
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
                $this->flashMessenger()->addMessage('Category Created Successfully');
                $this->redirect()->toRoute('admin/default',array('controller'=>'session','action'=>'groups'));

            }
            else{
                $output['message'] = __('save-failed-msg');
            }

        }


        $output['form'] = $form;
        $output['pageTitle']= __('Add Course Category');
        $output['action']='addgroup';
        $output['id']=null;
        return new ViewModel($output);


    }

    public function editgroupAction(){
        $output = array();
        $table = new SessionCategoryTable($this->getServiceLocator());

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

                $this->redirect()->toRoute('admin/default',array('controller'=>'session','action'=>'groups'));

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
        $output['pageTitle']= __('Edit Course Category');
        $output['row']= $row;
        $output['action']='editgroup';

        $viewModel = new ViewModel($output);
        $viewModel->setTemplate('admin/session/addgroup');
        return $viewModel ;
    }

    public function deletegroupAction(){
        $table = new SessionCategoryTable($this->getServiceLocator());
        $id = $this->params('id');

        try{
            $table->deleteRecord($id);
            $this->flashmessenger()->addMessage(__('Record deleted'));
        }
        catch(\Exception $ex){
            $this->deleteError();
        }

        $this->redirect()->toRoute('admin/default',array('controller'=>'session','action'=>'groups'));

    }

    public function createclassAction()
    {
        $output = array();
        $lessonTable = new LessonTable($this->getServiceLocator());
        $form = new LessonForm(null,$this->getServiceLocator());
        $filter = new LessonFilter();

        if ($this->request->isPost()) {

            $form->setInputFilter($filter);
            $data = $this->request->getPost();
            $form->setData($data);
            if ($form->isValid()) {

                $array = $form->getData();
                $array[$lessonTable->getPrimary()]=0;
                unset($array['lesson_group_id[]']);
                $id = $lessonTable->saveRecord($array);
                //    $this->flashmessenger()->addMessage(__('Changes Saved!'));
                $output['message'] = __('Record Added!');
                $form = new LessonForm(null,$this->getServiceLocator());
                $output['lesson_id'] = $id;

                $output['row']= $lessonTable->getRecord($id);

                $sessionForm = new CourseForm(null,$this->getServiceLocator());
                $output['form'] = $sessionForm;
                $viewModel = new ViewModel($output);
                $viewModel->setTerminal(true);
                return $viewModel;
            }
            else{
                $output['message'] = __('save-failed-msg');
                $messages=$form->getMessages();
          //      print_r($messages);
            //    print_r($output);
                exit();

            }

        }
        else{
            exit('Invalid request');
        }



    }


    public function statsAction()
    {
        $output = [];
        $id = $this->params('id');
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());

        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());
        $sessionTable = new SessionTable($this->getServiceLocator());
        $attendanceTable = new AttendanceTable($this->getServiceLocator());
        $testResultsTable = new StudentTestTable($this->getServiceLocator());

        $row = $studentSessionTable->getRecord($id);
        $output['row'] = $row;

        //get total classes
        $output['totalLessons'] = $sessionLessonTable->getSessionRecords($row->session_id)->count();


        //get list of classes student has attended
        $output['attended'] = $attendanceTable->getAttendedRecords($row->student_id,$row->session_id);

        if($output['totalLessons']>0){
            $output['percentage'] = 100 * ($output['attended']->count()/$output['totalLessons']);
        }
        else{
            $output['percentage'] = 0;
        }

        $output['percentage'] = round($output['percentage']);
        //get list of classes
        $lessons = $sessionLessonTable->getSessionRecords($row->session_id);
        $lessons->buffer();

        //get test results
        $studentTestTable = new StudentTestTable($this->getServiceLocator());
        $testResults = [];
        foreach($lessons as $lesson){

            if(!empty($lesson->test_required) && !empty($lesson->test_id) && $studentTestTable->hasTest($lesson->test_id,$row->student_id))
            {
                $testResults[$lesson->test_id] = $studentTestTable->getStudentRecord($row->student_id,$lesson->test_id);
            }

        }

        $output['testResults'] = $testResults;
        $output['pageTitle'] = __('Student Progress').': '.$row->first_name.' '.$row->last_name;

        $output['customCrumbs'] = [
            $this->url()->fromRoute('home') =>__('Home'),
            $this->url()->fromRoute('admin/default')=>__('Dashboard'),
            $this->url()->fromRoute('admin/default',['controller'=>'student','action'=>'sessions'])=>__('session-courses'),
            $this->url()->fromRoute('admin/default',['controller'=>'student','action'=>'sessionstudents','id'=>$row->session_id])=>__('Enrolled Students'),
            '#'=>__('Student Progress')
        ];


        return $output;
    }

    public function testsAction(){
        $id = $this->params('id');
        $sessionTestTable = new SessionTestTable($this->getServiceLocator());
        $sessionTable= new SessionTable($this->getServiceLocator());
        $row = $sessionTable->getRecord($id);

        $rowset = $sessionTestTable->getSessionRecords($id);
        $total = $rowset->count();
        return [
            'pageTitle'=>__('Tests for').'  '.$row->session_name.' ('.$total.')',
            'rowset'=>$rowset,
            'id'=>$id
        ];

    }

    public function addtestAction(){
        $id = $this->params('id');
        $sessionTestTable = new SessionTestTable($this->getServiceLocator());
        $sessionTable = new SessionTable($this->getServiceLocator());
        $sessionRow = $sessionTable->getRecord($id);
        $form = $this->getSessionTestForm();
        $output = [];
        if($this->request->isPost()){
            $formData = $this->request->getPost();
            $form->setData($formData);
            if($form->isValid()){

                $data = $form->getData();
                $data['session_id'] = $id;
                $data['opening_date']= strtotime($data['opening_date']);
                $data['closing_date']=strtotime($data['closing_date']);
                $sessionTestTable->addRecord($data);
                $this->flashMessenger()->addMessage(__('Test added successfully'));
                $this->redirect()->toRoute('admin/default',['controller'=>'session','action'=>'tests','id'=>$id]);


            }
            else{
                $output['message']= $this->getFormErrors($form);
            }
        }

        $output['form'] = $form;
        $output['pageTitle'] = __('Add Test to').' '.$sessionRow->session_name;
        $output['id']=$id;
        $output['customCrumbs'] = [
            $this->url()->fromRoute('home') =>__('Home'),
            $this->url()->fromRoute('admin/default')=>__('Dashboard'),
            $this->url()->fromRoute('admin/default',['controller'=>'student','action'=>'sessions'])=>__('Sessions/Courses'),
            $this->url()->fromRoute('admin/default',['controller'=>'session','action'=>'tests','id'=>$id])=>__('Tests'),
            '#'=>__('Add Test')
        ];
        return $output;
    }

    public function edittestAction(){
        $id = $this->params('id');
        $sessionTestTable = new SessionTestTable($this->getServiceLocator());
        $row = $sessionTestTable->getRecord($id);
        $sessionTable = new SessionTable($this->getServiceLocator());
        $sessionRow = $sessionTable->getRecord($row->session_id);
        $form = $this->getSessionTestForm();
        $output = [];
        if($this->request->isPost()){
            $formData = $this->request->getPost();
            $form->setData($formData);
            if($form->isValid()){

                $data = $form->getData();

                $data['opening_date']= strtotime($data['opening_date']);
                $data['closing_date']=strtotime($data['closing_date']);
                $sessionTestTable->update($data,$id);
                $this->flashMessenger()->addMessage('Session/Course saved successfully');
                $this->redirect()->toRoute('admin/default',['controller'=>'session','action'=>'tests','id'=>$sessionRow->session_id]);


            }
            else{
                $output['message']= $this->getFormErrors($form);
            }
        }
        else{
            $data = UtilityFunctions::getObjectProperties($row);
            if(!empty($data['opening_date']))
                $data['opening_date'] = date('Y-m-d',$row->opening_date);

            if(!empty($data['closing_date']))
                $data['closing_date'] = date('Y-m-d',$row->closing_date);

            $form->setData($data);

        }

        $output['form'] = $form;
        $output['pageTitle'] = __('Edit Test for').' '.$sessionRow->session_name;
        $viewModel = new ViewModel($output);
        $viewModel->setTemplate('admin/session/addtest');
        return $viewModel;
    }





    private function getSessionTestForm(){
        $form = new BaseForm();


        $options=array();



        $testTable = new TestTable($this->getServiceLocator());
        $rowset = $testTable->getLimitedRecords(1000);
        foreach($rowset as $row)
        {
            $options[$row->test_id]= $row->name;
        }

        $form->createSelect('test_id', 'Test', $options);
        $form->get('test_id')->setAttribute('class','form-control select2');


        $form->createText('opening_date','Opening Date (Optional)',false,'form-control date',null,__('Opening Date'));
        $form->createText('closing_date','Closing Date (Optional)',false,'form-control date',null,__('Closing Date'));

        $form->setInputFilter($this->getSessionTestFilter());
        return $form;


    }

    private function getSessionTestFilter(){
        $filter = new InputFilter();
        $filter->add([
            'name'=>'test_id',
            'required'=>true,
            'validators'=>[
                [
                    'name'=>'NotEmpty'
                ]
            ]
        ]);

        $filter->add([
            'name'=>'opening_date',
            'required'=>false
        ]);

        $filter->add([
            'name'=>'closing_date',
            'required'=>false
        ]);

        return $filter;

    }

    public function smssessionAction(){

        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $studentsTable = new StudentTable($this->getServiceLocator());
        $id = $this->params('id');

        $count = 0;

        if($this->request->isPost())
        {
            $message = $this->request->getPost('message');
            $mode = getenv('APP_MODE');

            if(!empty($id)){
                $totalRecords = $studentSessionTable->getTotalForSession($id);
            }
            else{
                $totalRecords = Student::count();
            }

            $rowsPerPage = 3000;
            $totalPages = ceil($totalRecords/$rowsPerPage);

            $numbers = [];
            for($i=1;$i<=$totalPages;$i++){
                if(!empty($id)){
                    $paginator = $studentSessionTable->getSessionRecords(true,$id,true);
                }
                else{
                    $paginator = $studentsTable->getPaginatedRecords(true);
                }


                $paginator->setCurrentPageNumber($i);
                $paginator->setItemCountPerPage($rowsPerPage);

                foreach ($paginator as $row){
                    if(!empty($row->mobile_number)){
                        $numbers[] = $row->mobile_number;
                        $count++;
                    }

                }

            }


            $smsGateway = new SmsGateway($this->getServiceLocator(),$numbers,$message);
            $response= $smsGateway->send();


            $this->flashMessenger()->addMessage($response);

        }

            return $this->goBack();


    }


}