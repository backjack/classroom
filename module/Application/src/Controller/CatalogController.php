<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/3/2017
 * Time: 2:12 PM
 */

namespace Application\Controller;


use Application\Form\DiscussionForm;
use Application\Model\DownloadFileTable;
use Application\Model\DownloadSessionTable;
use Application\Model\SessionCategoryTable;
use Application\Model\SessionInstructorTable;
use Application\Model\SessionLessonAccountTable;
use Application\Model\SessionLessonTable;
use Application\Model\SessionTable;
use Application\Model\SessionTestTable;
use Application\Model\StudentLectureTable;
use Application\Model\StudentSessionLogTable;
use Application\Model\StudentSessionTable;
use Application\Model\StudentTestTable;
use Application\Model\TestQuestionTable;
use Intermatics\HelperTrait;
use Intermatics\UtilityFunctions;
use Zend\EventManager\EventManagerInterface;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CatalogController extends AbstractController {
    use HelperTrait;
    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);
        $controller = $this;
        $events->attach('dispatch', function ($e) use ($controller) {
            $controller->layout('layout/student');
        }, 100);
    }



    /**
     * For browsing sessions
     */
    public function sessionsAction(){

        if(!$this->studentIsLoggedIn()){
            $this->layout('layout/layout');

        }

        $table = new SessionTable($this->getServiceLocator());
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());



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
        $text->setAttribute('placeholder','Search');
        $text->setValue($filter);


        $sortSelect = new Select('sort');
        $sortSelect->setAttribute('class','form-control');
        //$sortSelect->setAttribute('style','max-width:100px');

        $valueOptions = [
            'recent'=>__('Recently Added'),
            'asc'=>__('Alphabetical (Ascending)'),
            'desc'=>__('Alphabetical (Descending)'),
            'date'=>__('Start Date'),
        ];

        if($this->getSetting('general_show_fee')==1){
            $valueOptions['priceAsc'] = __('Price (Lowest to Highest)');
            $valueOptions['priceDesc'] = __('Price (Highest to Lowest)');
        }

        $sortSelect->setValueOptions($valueOptions);
        $sortSelect->setEmptyOption('--'.__('Sort').'--');
        $sortSelect->setValue($sort);


        $groupTable = new SessionCategoryTable($this->getServiceLocator());
        $groupRowset = $groupTable->getLimitedRecords(100);


        $paginator = $table->getPaginatedRecords(true,null,true,$filter,$group,$sort,['s','b'],true);




        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);

        $authService=$this->getAuthService();
        $role = UtilityFunctions::getRole();


        if($authService->hasIdentity() && $role=='student'){
            $id = $this->getId();
        }
        else{
            $id = 0;
        }

        $output = array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Upcoming Sessions'),
            'studentSessionTable'=>$studentSessionTable,
            'id'=>$id,
            'filter'=>$filter,
            'group'=>$group,
            'text'=>$text,
            'sortSelect'=>$sortSelect,
            'sort'=>$sort,
        );

        if(!$this->studentIsLoggedIn()){

            return $this->getViewModel($output);
        }
        else{
            return new ViewModel($output);
        }


    }

    /**
     * For browsing courses
     */
    public function coursesAction(){
        if(!$this->studentIsLoggedIn()){
            $this->layout('layout/layout');
        }

        $table = new SessionTable($this->getServiceLocator());
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $sessionCategoryTable = new SessionCategoryTable($this->getServiceLocator());


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
        $text->setAttribute('placeholder',__('Search'));
        $text->setValue($filter);


        $sortSelect = new Select('sort');
        $sortSelect->setAttribute('class','form-control');
        //$sortSelect->setAttribute('style','max-width:100px');

        $valueOptions = [
            'recent'=>__('Recently Added'),
            'asc'=>__('Alphabetical (Ascending)'),
            'desc'=>__('Alphabetical (Descending)'),
            'date'=>__('Start Date'),
        ];

        if($this->getSetting('general_show_fee')==1){
            $valueOptions['priceAsc'] = __('Price (Lowest to Highest)');
            $valueOptions['priceDesc'] = __('Price (Highest to Lowest)');
        }

        $sortSelect->setValueOptions($valueOptions);
        $sortSelect->setEmptyOption('--'.__('Sort').'--');
        $sortSelect->setValue($sort);


        $groupTable = new SessionCategoryTable($this->getServiceLocator());
        $groupRowset = $groupTable->getActiveRecords(100);


        $paginator = $table->getPaginatedCourseRecords(true,null,true,$filter,$group,$sort,'c');




        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);

        $authService=$this->getAuthService();
        $role = UtilityFunctions::getRole();


        if($authService->hasIdentity() && $role=='student'){
            $id = $this->getId();
        }
        else{
            $id = 0;
        }

        $categories = $sessionCategoryTable->getLimitedRecords(100);

        $pageTitle = __('Online Courses');
        if(!empty($group)){
            $categoryRow = $sessionCategoryTable->getRecord($group);
            $pageTitle .=': '.$categoryRow->category_name;
            $description = $categoryRow->description;
        }
        else{
            $description = '';
        }


        $output = array(
            'paginator'=>$paginator,
            'pageTitle'=>$pageTitle,
            'studentSessionTable'=>$studentSessionTable,
            'id'=>$id,
            'filter'=>$filter,
            'group'=>$group,
            'text'=>$text,
            'sortSelect'=>$sortSelect,
            'sort'=>$sort,
            'categories'=>$categories,
            'description'=>$description
        );

        if(!$this->studentIsLoggedIn()){
            return $this->getViewModel($output);
        }
        else{
            return new ViewModel($output);
        }

    }

    public function courseAction(){
        if(!$this->studentIsLoggedIn()){
            $this->layout('layout/layout');
        }

        $authService=$this->getAuthService();
        $role = UtilityFunctions::getRole();
        $id = $this->params('id');
        $sessionTable = new SessionTable($this->getServiceLocator());
        $sessionLessonTable = new SessionLessonTable($this->getServiceLocator());
        $sessionLessonAccountTable = new SessionLessonAccountTable($this->getServiceLocator());
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $sessionInstructorTable = new SessionInstructorTable($this->getServiceLocator());
        $studentLectureTable = new StudentLectureTable($this->getServiceLocator());
        $logTable = new StudentSessionLogTable($this->getServiceLocator());
        $enrolled = false;
        $resumeLink = null;
        if($authService->hasIdentity() && $role=='student') {
            $studentId = $this->getId();
            if ($studentSessionTable->enrolled($studentId, $id)) {
            $enrolled = true;
                //check if student has started lecture
                if($studentLectureTable->hasLecture($studentId,$id)){
                    $lecture = $studentLectureTable->getLecture($studentId,$id);
                    if($lecture->sort_order == 1){
                        $resumeLink = $this->url()->fromRoute('view-class', ['classId' => $lecture->lesson_id, 'sessionId' => $id]);
                    }
                    else{
                        $resumeLink = $this->url()->fromRoute('view-lecture', ['lectureId' => $lecture->lecture_id, 'sessionId' => $id]);

                    }

                }
                else{

                    $resumeLink = $this->url()->fromRoute('application/default', ['controller' => 'course', 'action' => 'intro','id'=>$id]);

                }

            }

        }
        else{
            $studentId = 0;
        }

        $discussionForm= new DiscussionForm(null,$this->getServiceLocator(),$studentId);
        $downloadSessionTable = new DownloadSessionTable($this->getServiceLocator());

        $row = $sessionTable->getRecord($id);
        $rowset = $sessionLessonTable->getSessionRecords($id);

        //ensure it is an online course
        if($row->session_type!='c'){
            $this->redirect()->toRoute('session-details',['id'=>$row->session_id]);
        }

        //get instructors
        $instructors = $sessionInstructorTable->getSessionRecords($id);

        //get downloads
        $downloads = $downloadSessionTable->getSessionRecords($id);

        //check if student has started course
        //get session tests
        $sessionTestTable  = new SessionTestTable($this->getServiceLocator());
        $tests = $sessionTestTable->getSessionRecords($id);

        $output = ['rowset'=>$rowset,'row'=>$row,'pageTitle'=>__('Course Details'),'table'=>$sessionLessonAccountTable,'id'=>$id,
            'studentId'=>$studentId,
            'studentSessionTable'=>$studentSessionTable,
            'instructors' => $instructors,
            'form'=>$discussionForm,
            'downloads'=>$downloads,
            'fileTable'=> new DownloadFileTable($this->getServiceLocator()),
            'resumeLink'=>$resumeLink,
            'enrolled'=>$enrolled,
            'tests'=>$tests,
            'questionTable'=>new TestQuestionTable($this->getServiceLocator()),
            'studentTest'=> new StudentTestTable($this->getServiceLocator())
        ];

        if(!$this->studentIsLoggedIn()){
            return $this->getViewModel($output);
        }
        else{
            return new ViewModel($output);
        }


    }


    public function getAuthService()
    {

        return $this->getServiceLocator()->get('StudentAuthService');
    }

}