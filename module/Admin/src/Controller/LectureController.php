<?php

namespace Admin\Controller;
require_once 'vendor/phpQuery/phpQuery.php';
use Application\Controller\AbstractController;
use Application\Entity\Lecture;
use Application\Entity\LecturePage;
use Application\Entity\Video;
use Application\Form\LectureForm;
use Application\Model\LectureFileTable;
use Application\Model\LecturePageTable;
use Application\Model\LectureTable;
use Application\Model\LessonTable;
use Embed\Embed;
use Gufy\PdfToHtml\Pdf;
use Intermatics\BaseForm;
use Intermatics\HelperTrait;
use NcJoes\OfficeConverter\OfficeConverter;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Form\LectureFilter;
use Intermatics\UtilityFunctions;
use Zend\Form\Element\File;
use Zend\InputFilter\Input;
use Zend\Validator\File\Extension;
use Zend\Validator\File\Size;


/**
 * NewsController
 *
 * @author
 *
 * @version
 *
 */
if(!defined('DIR_MER_IMAGE')) define('DIR_MER_IMAGE', 'public/');
class LectureController extends AbstractController {
    use HelperTrait;
    private $count =0;
    private $images = [];
    public function indexAction() {
        // TODO Auto-generated NewssController::indexAction() default action
        $table = new LectureTable($this->getServiceLocator());
        $lessonTable = new LessonTable($this->getServiceLocator());
        $id = $this->params('id');
        $paginator = $table->getPaginatedRecords(true,$id);
        $lessonRow = $lessonTable->getRecord($id);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        return new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Class Lectures').': '.$lessonRow->lesson_name,
            'id'=>$id,
            'lectureFileTable'=> new LectureFileTable($this->getServiceLocator()),
            'lecturePageTable'=> new LecturePageTable($this->getServiceLocator()),
            'lesson'=>$lessonRow
        ));


    }

    public function addAction()
    {
        $output = array();
        $lectureTable = new LectureTable($this->getServiceLocator());
        $form = new LectureForm(null,$this->getServiceLocator());
        $filter = new LectureFilter();
        $id = $this->params('id');

        if ($this->request->isPost()) {

            $form->setInputFilter($filter);
            $data = $this->request->getPost();
            $form->setData($data);
            if ($form->isValid()) {

                $array = $form->getData();
                $array[$lectureTable->getPrimary()]=0;
                $array['lesson_id']=$id;

                if(empty($array['sort_order'])){
                    $array['sort_order'] = $lectureTable->getNextSortOrder($id);
                }

                $lectureId = $lectureTable->saveRecord($array);
                $lectureTable->arrangeSortOrders($id);

                $output['message'] = __('Record Added!');
                $form = new LectureForm(null,$this->getServiceLocator());
                $this->flashMessenger()->addMessage('Lecture Added. Now add some content to it!');
                $this->redirect()->toRoute('admin/default',array('controller'=>'lecture','action'=>'content','id'=>$lectureId));
            }
            else{


                $output['message'] = __('save-failed-msg');

            }

        }

        $output['form'] = $form;
        $output['pageTitle']= __('Add Lecture');
        $output['action']='add';
        $output['id']=$id;
        $output['customCrumbs'] = [
            $this->url()->fromRoute('home') =>__('Home'),
            $this->url()->fromRoute('admin/default')=>__('Dashboard'),
            $this->url()->fromRoute('admin/default',['controller'=>'lesson','action'=>'index'])=>__('Classes'),
            $this->url()->fromRoute('admin/default',['controller'=>'lecture','action'=>'index','id'=>$id])=>__('Class Lectures'),
            '#'=>__('Add Lecture')
        ];
        return new ViewModel($output);
    }

    public function editAction(){
        $output = array();
        $lectureTable = new LectureTable($this->getServiceLocator());
        $form = new LectureForm(null,$this->getServiceLocator());
        $filter = new LectureFilter();
        $id = $this->params('id');

        $row = $lectureTable->getRecord($id);
        if ($this->request->isPost()) {

            $form->setInputFilter($filter);
            $data = $this->request->getPost();

            $form->setData($data);
            if ($form->isValid()) {

                //add groups
                $array = $form->getData();


                $array[$lectureTable->getPrimary()]=$id;
                if(empty($array['sort_order'])){
                    $array['sort_order'] = $lectureTable->getNextSortOrder($row->lesson_id);
                }
                $lectureTable->saveRecord($array);
                //    $this->flashmessenger()->addMessage(__('Changes Saved!'));
                $output['message'] = __('Changes Saved!');
                $row = $lectureTable->getRecord($id);
                $lectureTable->arrangeSortOrders($row->lesson_id);
                $this->flashMessenger()->addMessage(__('Changes Saved!'));
                $this->redirect()->toRoute('admin/default',array('controller'=>'lecture','action'=>'index','id'=>$row->lesson_id));

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
        $output['pageTitle']= __('Edit Lecture');
        $output['row']= $row;
        $output['action']='edit';
        $output['customCrumbs'] = [
            $this->url()->fromRoute('home') =>__('Home'),
            $this->url()->fromRoute('admin/default')=>__('Dashboard'),
            $this->url()->fromRoute('admin/default',['controller'=>'lesson','action'=>'index'])=>__('Classes'),
            $this->url()->fromRoute('admin/default',['controller'=>'lecture','action'=>'index','id'=>$row->lesson_id])=>__('Class Lectures'),
            '#'=>__('Edit Lecture')
        ];
        $viewModel = new ViewModel($output);
        $viewModel->setTemplate('admin/lecture/add');
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
        $table = new LectureTable($this->getServiceLocator());
        $id = $this->params('id');
        $row = $table->getRecord($id);
        $lessonId = $row->lesson_id;
        try{
            $table->deleteRecord($id);
            $this->flashmessenger()->addMessage(__('Record deleted'));
        }
        catch(\Exception $ex){
            $this->deleteError();
        }

        $this->redirect()->toRoute('admin/default',array('controller'=>'lecture','action'=>'index','id'=>$lessonId));
    }

    public function filesAction(){

        $id = $this->params('id');
        $table = new LectureFileTable($this->getServiceLocator());

        $lectureTable = new LectureTable($this->getServiceLocator());
        $lectureRow = $lectureTable->getRecord($id);
        $rowset = $table->getDownloadRecords($id);
        $output = [];
        $output['customCrumbs'] = [
            $this->url()->fromRoute('home') =>__('Home'),
            $this->url()->fromRoute('admin/default')=>__('Dashboard'),
            $this->url()->fromRoute('admin/default',['controller'=>'lesson','action'=>'index'])=>__('Classes'),
            $this->url()->fromRoute('admin/default',['controller'=>'lecture','action'=>'index','id'=>$lectureRow->lesson_id])=>__('Class Lectures'),
            '#'=>__('Lecture Downloads')
        ];
        $output['rowset'] = $rowset;
        $output['pageTitle'] = __('Lecture Downloads').': '.$lectureRow->lecture_title;
        $output['id'] = $id;
        $viewModel = new ViewModel($output);

        return $viewModel;
    }

    public function addfileAction(){
        $path = $this->request->getQuery('path');
        $id = $this->params('id');

        $downloadFileTable = new LectureFileTable($this->getServiceLocator());
        $path = str_ireplace('usermedia/','',$path);
        if(!$downloadFileTable->fileExists($id,$path)){
            $downloadFileTable->addRecord([
                'lecture_id'=>$id,
                'path'=>$path,
                'created_on'=>time(),
                'status'=>1
            ]);
        }


        $filesViewModel = $this->forward()->dispatch('Admin\Controller\Lecture',['action'=>'files','id'=>$id]);
        $filesViewModel->setTerminal(true);
        return $filesViewModel;
    }

    public function removefileAction(){

        $id = $this->params('id');
        $downloadFileTable = new LectureFileTable($this->getServiceLocator());
        $row = $downloadFileTable->getRecord($id);
        $downloadId = $row->lecture_id;

        $downloadFileTable->deleteRecord($id);
        $filesViewModel = $this->forward()->dispatch('Admin\Controller\Lecture',['action'=>'files','id'=>$downloadId]);
        $filesViewModel->setTerminal(true);
        return $filesViewModel;
    }

    public function downloadAction(){
        set_time_limit(86400);
        $id = $this->params('id');
        $table = new LectureFileTable($this->getServiceLocator());
        $row = $table->getRecord($id);
        $path = 'public/usermedia/'.$row->path;



        header('Content-type: '.getFileMimeType($path));

// It will be called downloaded.pdf
        header('Content-Disposition: attachment; filename="'.basename($path).'"');

// The PDF source is in original.pdf
        readfile($path);
        exit();
    }

    public function contentAction(){
        $id = $this->params('id');
        $table = new LecturePageTable($this->getServiceLocator());

        $lectureTable = new LectureTable($this->getServiceLocator());
        $lectureRow = $lectureTable->getRecord($id);
        $id = $this->params('id');
        $paginator = $table->getPaginatedRecords(true,$id);


        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(1000);

        $output = [];
        $output['customCrumbs'] = [
            $this->url()->fromRoute('home') =>__('Home'),
            $this->url()->fromRoute('admin/default')=>__('Dashboard'),
            $this->url()->fromRoute('admin/default',['controller'=>'lesson','action'=>'index'])=>__('Classes'),
            $this->url()->fromRoute('admin/default',['controller'=>'lecture','action'=>'index','id'=>$lectureRow->lesson_id])=>__('Class Lectures'),
            '#'=>__('Lecture Content')
        ];
        $output['paginator'] = $paginator;
        $output['pageTitle'] = __('Lecture Content').': '.$lectureRow->lecture_title;
        $output['id'] = $id;
        $output['no_image']= resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
        $output['display_image']= resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
        $viewModel = new ViewModel($output);

        return $viewModel;
    }

    public function reorderAction(){
       if(!empty($_REQUEST['row'])){

           $counter = 1;
           foreach($_REQUEST['row'] as $id){
               $lecturePage = LecturePage::find($id);
               if($lecturePage){
                   $lecturePage->sort_order = $counter;
                   $lecturePage->save();
                   $counter++;
               }

           }

       }
        exit('done');
    }

    public function deletecontentsAction(){
        $count = 0;
        if($this->request->isPost()){

            foreach($this->request->getPost() as $key=>$value){
                if($value>0){
                    try{
                        $lecturePage = LecturePage::find($value);
                        $lecturePage->delete();
                        $count++;

                    }
                    catch(\Exception $ex){

                    }

                }
            }
            $this->flashMessenger()->addMessage($count.' '.__('items deleted'));
        }

        return $this->goBack();
    }

    public function addcontentAction(){

        $table = new LecturePageTable($this->getServiceLocator());
        $id = $this->params('id');
        $form = $this->getContentForm();
        $form->setInputFilter($this->getContentFilter());
        $output = [];
        if($this->request->isPost()){
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);
            if($form->isValid()){
                $data = $form->getData();
                $data['lecture_id'] = $id;

                if(empty($data['sort_order'])){
                    $data['sort_order'] = $table->getNextSortOrder($id);
                }

                $data['lecture_page_id'] = $formData['lecture_page_id'];
                $table->saveRecord($data);
                $table->arrangeSortOrders($id);
                $this->flashMessenger()->addMessage(__('Content added'));
           }
            else{
                $this->flashMessenger()->addMessage($this->getFormErrors($form));
            }
        }

        $this->redirect()->toRoute('admin/default',['controller'=>'lecture','action'=>'content','id'=>$id]);


    }

    public function addvideoAction(){
        $table = new LecturePageTable($this->getServiceLocator());
        $id = $this->params('id');
        if($this->request->isPost()){
            $formData = $this->request->getPost();
            $url = trim($formData['url']);

            if(!empty($url)){

            $data = $formData->toArray();
                unset($data['url']);
            $data['lecture_id'] = $id;

            if(empty($data['sort_order'])){
                $data['sort_order'] = $table->getNextSortOrder($id);
            }


            //get link from url
                try{
                    $info = Embed::create($url);

                    if($info->type != 'video'){
                        $this->flashMessenger()->addMessage(__('invalid-v-url-msg'));
                        $this->goBack();
                    }
                    $data['title'] = $info->title;
                    $data['content']= $info->code;

                    $table->addRecord($data);
                    $table->arrangeSortOrders($id);
                    $this->flashMessenger()->addMessage(__('Video added'));

                }
                catch(\Exception $ex){

                    $this->flashMessenger()->addMessage(__('error-occurred-msg').': '.$ex->getMessage());

                }


            }
            else
            {
                $this->flashMessenger()->addMessage(__('supply-valid-url'));
            }


        }

        $this->goBack();
    }

    public function addzoomAction(){
        $table = new LecturePageTable($this->getServiceLocator());
        $id = $this->params('id');
        if($this->request->isPost()){
            $formData = $this->request->getPost();
            $form = $this->getZoomForm();
            $form->setData($formData);

            if($form->isValid()){

                $data = $form->getData();
                $data['lecture_id'] = $id;

                if(empty($data['sort_order'])){
                    $data['sort_order'] = $table->getNextSortOrder($id);
                }


                //get link from url
                try{

                    $data['content']= serialize($data);

                    $table->addRecord([
                        'lecture_id'=>$data['lecture_id'],
                        'title'=>$data['title'],
                        'content'=>$data['content'],
                        'sort_order'=>$data['sort_order'],
                        'type'=>'z'
                    ]);
                    $table->arrangeSortOrders($id);
                    $this->flashMessenger()->addMessage(__('record-added!'));

                }
                catch(\Exception $ex){

                    $this->flashMessenger()->addMessage($ex->getMessage());

                }


            }
            else
            {
                $this->flashMessenger()->addMessage($this->getFormErrors($form));
            }


        }

        $this->goBack();
    }

    public function editzoomAction(){
        $id = $this->params('id');
        $lecturePage = LecturePage::findOrFail($id);
        $table = new LecturePageTable($this->getServiceLocator());

        if($this->request->isPost()){
            $form = $this->getZoomForm();
            $formData = $this->request->getPost();
            $form->setData($formData);
            if($form->isValid()){
                $data = $form->getData();

                try{

                    $data['content']= serialize($data);

                    $table->update([
                        'title'=>$data['title'],
                        'content'=>$data['content'],
                        'sort_order'=>$data['sort_order'],
                    ],$id);
                    $table->arrangeSortOrders($id);
                    $this->flashMessenger()->addMessage(__('changes-saved!'));

                }
                catch(\Exception $ex){

                    $this->flashMessenger()->addMessage($ex->getMessage());

                }
            }
            else{
                $this->flashMessenger()->addMessage($this->getFormErrors($form));
            }
           return $this->goBack();
        }

        $data = unserialize($lecturePage->content);
        $data['title'] = $lecturePage->title;
        $data['sort_order'] = $lecturePage->sort_order;

        $viewModel = new ViewModel([
            'data'=> $data,
            'id'=>$id
        ]);
        $viewModel->setTerminal(true);

        return $viewModel;

    }



    public function importpdfAction(){
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING );
        set_time_limit(3600);
       if(getenv('APP_MODE') != 'live' ){
           // change pdftohtml bin location
           \Gufy\PdfToHtml\Config::set('pdftohtml.bin', 'C:/poppler-0.51/bin/pdftohtml.exe');

           \Gufy\PdfToHtml\Config::set('pdfinfo.bin', 'C:/poppler-0.51/bin/pdfinfo.exe');
       }

        $user= '';
        if(defined('USER_ID')){
            $user = '/'.USER_ID;
        }
        $filePath = 'public/usermedia'.$user;
        $userBaseUrl = $this->getBaseUrl() . '/usermedia'.$user;



        if(!$this->hasPermission('misc/global_access')){

            if(!is_dir(USER_PATH.'/admin_files')){
                mkdir(USER_PATH.'/admin_files') or die('unable to make folder');
            }

            if(!is_dir(USER_PATH.'/admin_files/'.$this->getAdminId())){
                mkdir(USER_PATH.'/admin_files/'.$this->getAdminId()) or die('unable to make user folder');
            }


            $basePath = USER_PATH.'/admin_files/'.$this->getAdminId().'/pdf_import';
            if(!is_dir($basePath)){
                mkdir($basePath) or die('unable to create subdirectory: '.$basePath);
            }

            $fileUrl = $userBaseUrl.'/admin_files/'.$this->getAdminId().'/pdf_import';
            define('BASE_URL',$fileUrl);

        }else{
            $basePath= USER_PATH.'/pdf_import';
            if(!is_dir($basePath)){
                mkdir($basePath) or die('unable to create pdf directory');
            }

            $fileUrl = $userBaseUrl.'/pdf_import';
            define('BASE_URL',$fileUrl);
        }


        $basePath = realpath($basePath);

        define('BASE_PATH',$basePath);




        // change output file location
        define('IMAGE_PATH', BASE_PATH . '/'.date('Y_m_d') . DIRECTORY_SEPARATOR);
        define('IMAGE_URL', BASE_URL . '/'.date('Y_m_d') .'/');
// BASE_PATH and BASE_URL set elsewhere in the script
        list($fn, $ext) = explode('.', $filename);

        \Gufy\PdfToHtml\Config::set('pdftohtml.output', IMAGE_PATH . "{$fn}/");

        $id = $this->params('id');
        $table = new LecturePageTable($this->getServiceLocator());
        $lecture = Lecture::find($id);

        $form = $this->getPdfForm();
        $output =[];

        if($this->request->isPost())
        {

            $postData = $this->request->getPost();
            $form->setData($postData);
            if($form->isValid()){
                $data = $form->getData();
                $type = $data['type'];
                $path =  'public/'.$postData['path'];
                $title = $data['title'];

                $pdf = new Pdf($path);
                // check if your pdf has more than one pages
                $total_pages = $pdf->getPages();

                if($type=='all'){

                    for($i=1;$i<=$total_pages;$i++){

                        $page = $pdf->html($i);
                        if(substr_count($page,'src="//')>0){
                            $page = str_replace('src="//', 'src="'.IMAGE_URL, $page);
                        }
                        else{
                            $page = str_replace('src="', 'src="'.IMAGE_URL, $page);
                            //$page = str_replace('src="', 'src="'.$userBaseUrl.'/', $page);
                        }
                        //
                        //
                        $page = $this->formatPdfPage($page);
                        if(empty($page)){
                            continue;
                        }

                        $lecturePage = new LecturePage();
                        $lecturePage->sort_order = $table->getNextSortOrder($id);
                        $lecturePage->lecture_id =$id;
                        $lecturePage->title = $title ." ($i)";
                        $lecturePage->content = $page;
                        $lecturePage->type= 't';
                        $lecturePage->save();

                    }
                    $table->arrangeSortOrders($id);
                    $this->flashmessenger()->addMessage('PDF file imported. '.$total_pages.' pages created');


                }
                elseif($type=='range'){
                    $start = $data['start'];
                    $end = $data['end'];


                    if($end > $total_pages){
                        $end = $total_pages;
                    }
                    $count =  0;

                    if($end<= $start){
                        $this->flashMessenger()->addMessage('The end page must be greater than the start page');
                       return $this->redirect()->toUrl(selfURL());

                    }

                    for($i=$start;$i<=$end;$i++){

                        $page = $pdf->html($i);
                        if(substr_count($page,'src="//')>0){
                            $page = str_replace('src="//', 'src="'.IMAGE_URL, $page);
                        }
                        else{
                            $page = str_replace('src="', 'src="'.IMAGE_URL, $page);
                           // $page = str_replace('src="', 'src="'.$userBaseUrl.'/', $page);
                        }
                        $page = $this->formatPdfPage($page);
                        if(empty($page)){
                            continue;
                        }
                        $count++;
                        $lecturePage = new LecturePage();
                        $lecturePage->sort_order = $table->getNextSortOrder($id);
                        $lecturePage->lecture_id =$id;
                        $lecturePage->title = $title ." ($count)";
                        $lecturePage->type= 't';
                        $lecturePage->content = $page;
                        $lecturePage->save();

                    }
                    $table->arrangeSortOrders($id);
                    $this->flashmessenger()->addMessage('PDF file imported. '.$count.' pages created');



                }
                elseif($type='choose'){

                    $pages = $data['pages'];
                    $pageArray = explode(',',$pages);
                    $count =  0;

                    foreach($pageArray as $value){
                        $pageNo = intval($value);
                        if(empty($pageNo) || $pageNo > $total_pages ){
                            continue;
                        }

                        $page = $pdf->html($pageNo);
                        if(substr_count($page,'src="//')>0){
                            $page = str_replace('src="//', 'src="'.IMAGE_URL, $page);
                        }
                        else{
                            $page = str_replace('src="', 'src="'.IMAGE_URL, $page);
                            //$page = str_replace('src="', 'src="'.$userBaseUrl.'/', $page);
                        }
                        $page = $this->formatPdfPage($page);

                        if(empty($page)){
                            continue;
                        }
                        $count++;
                        $lecturePage = new LecturePage();
                        $lecturePage->sort_order = $table->getNextSortOrder($id);
                        $lecturePage->lecture_id =$id;
                        $lecturePage->title = $title ." ($count)";
                        $lecturePage->type= 't';
                        $lecturePage->content = $page;
                        $lecturePage->save();

                    }

                    $table->arrangeSortOrders($id);
                    $this->flashmessenger()->addMessage('PDF file imported. '.$count.' pages created');

                }
                $this->cropImages();
               return $this->redirect()->toRoute('admin/default',['controller'=>'lecture','action'=>'content','id'=>$id]);

            }
            else{
            $output['message'] = $this->getFormErrors($form);

            }



        }

        $output['lecture']= $lecture;
        $output['customCrumbs'] = [
            $this->url()->fromRoute('home') =>__('Home'),
            $this->url()->fromRoute('admin/default')=>__('Dashboard'),
            $this->url()->fromRoute('admin/default',['controller'=>'lesson','action'=>'index'])=>__('Classes'),
            $this->url()->fromRoute('admin/default',['controller'=>'lecture','action'=>'index','id'=>$lecture->lesson->lesson_id])=>__('Class Lectures'),
            $this->url()->fromRoute('admin/default',['controller'=>'lecture','action'=>'content','id'=>$lecture->lecture_id])=>'Lecture Content',
            '#'=>'Import PDF'
        ];
        $output['pageTitle'] = 'Import PDF to lecture: '.$lecture->lecture_title;
        $output['form'] = $form;
        return $output;
    }

    public function importpptAction(){
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING );
        set_time_limit(3600);


        $user= '';
        if(defined('USER_ID')){
            $user = '/'.USER_ID;
        }
        $filePath = 'public/usermedia'.$user;
        $userBaseUrl = 'usermedia'.$user;



        // change output file location

        $id = $this->params('id');
        $table = new LecturePageTable($this->getServiceLocator());
        $lecture = Lecture::find($id);

        $form = $this->getPptForm();
        $output =[];

        if($this->request->isPost())
        {



            $postData = $this->request->getPost();
            $form->setData($postData);
            if($form->isValid()){

                $time = time();
                $oFilePath = $this->request->getPost('path');
                $baseName= basename($oFilePath);
                $safeName= safeUrl($baseName);
                $safeName= substr($safeName,0,15);
                $time = $safeName.'_'.$time;

                if(!$this->hasPermission('misc/global_access')){

                    if(!is_dir(USER_PATH.'/admin_files')){
                        mkdir(USER_PATH.'/admin_files') or die('unable to make folder');
                    }

                    if(!is_dir(USER_PATH.'/admin_files/'.$this->getAdminId())){
                        mkdir(USER_PATH.'/admin_files/'.$this->getAdminId()) or die('unable to make user folder');
                    }


                    $basePath = USER_PATH.'/admin_files/'.$this->getAdminId().'/ppt_import';
                    if(!is_dir($basePath)){
                        mkdir($basePath) or die('unable to create subdirectory: '.$basePath);
                    }

                    $basePath = $basePath. '/'.date('Y_m_d');
                    if(!is_dir($basePath)){
                        mkdir($basePath) or die('unable to create subdirectory: '.$basePath);
                    }

                    $pptFolder = $basePath.'/'.$time;

                    if(!is_dir($pptFolder)){
                        mkdir($pptFolder) or die('unable to create subdirectory: '.$pptFolder);
                    }

                    $fileUrl = $userBaseUrl.'/admin_files/'.$this->getAdminId().'/ppt_import/'.date('Y_m_d').'/'.$time;
                    define('BASE_URL',$fileUrl);

                }else{
                    $basePath= USER_PATH.'/ppt_import';
                    if(!is_dir($basePath)){
                        mkdir($basePath) or die('unable to create ppt directory');
                      //  chmod($basePath, 0777);
                    }

                    $basePath = $basePath. '/'.date('Y_m_d');
                    if(!is_dir($basePath)){
                        mkdir($basePath) or die('unable to create subdirectory: '.$basePath);
                      //  chmod($basePath, 0777);
                    }


                    $pptFolder = $basePath.'/'.$time;

                    if(!is_dir($pptFolder)){
                        mkdir($pptFolder) or die('unable to create subdirectory: '.$pptFolder);
                     //   chmod($pptFolder, 0777);
                    }


                    $fileUrl = $userBaseUrl.'/ppt_import/'.date('Y_m_d').'/'.$time;
                    define('BASE_URL',$fileUrl);
                }


                $basePath = realpath($pptFolder);

                define('BASE_PATH',$basePath);




                $data = $form->getData();
                $type = $data['type'];
                $path =  'public/'.$postData['path'];
                $title = $data['title'];

                $fileName = basename($path);
                $newFile = BASE_PATH.'/'.$fileName;

                //ensure file is a valid ppt document
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $ext = strtolower(trim($ext));


                if($ext != 'ppt' && $ext != 'pptx' && $ext != 'odp')
                {
                    $this->flashMessenger()->addMessage('This is not a valid Powerpoint/ODP file: '.$fileName);
                    return $this->redirect()->toUrl(selfURL());
                }


                //first copy the file into our new directory
                if(!copy($path,$newFile)){
                    $this->flashMessenger()->addMessage('Unable to copy file');
                    return $this->redirect()->toUrl(selfURL());
                }


                //now convert file to pdf
                $converter = new OfficeConverter($newFile);
                $result = $converter->convertTo('output.pdf');

                $pdfFile = BASE_PATH.'/output.pdf';
                //now convert pdf file to images
                chdir(BASE_PATH);
               //shell_exec("convert -density 400 $pdfFile -resize 2000x1500 image%d.jpg");

                //get total number of pages in pdf file
                $pdf = new \Spatie\PdfToImage\Pdf($pdfFile);
                $pdf->setCompressionQuality(80);
                $pages = $pdf->getNumberOfPages();

                for($i=1;$i<=$pages;$i++){
                    $pdf->setPage($i)->saveImage(BASE_PATH."/image$i.jpg");

                }



                //delete files
                unlink($pdfFile);
                unlink($fileName);
                $counter=0;
                //scan directory to get all images
                $fi = new \FilesystemIterator(__DIR__, \FilesystemIterator::SKIP_DOTS);
                $totalFiles = iterator_count($fi);

                for($i=1;$i <= $pages;$i++){

                    $imageName= 'image'.$i.'.jpg';

                    if(is_file($imageName) && $this->isImage($imageName))
                    {

                        $lecturePage = new LecturePage();
                        $lecturePage->sort_order = $table->getNextSortOrder($id);
                        $lecturePage->lecture_id =$id;
                        $lecturePage->title = $title .' ('.($counter+1).')';
                        $lecturePage->content = BASE_URL.'/'.$imageName ;
                        $lecturePage->type= 'i';
                        $lecturePage->save();
                        $counter++;
                    }
                }

                $this->flashMessenger()->addMessage("$counter slides imported successfully");

                return $this->redirect()->toRoute('admin/default',['controller'=>'lecture','action'=>'content','id'=>$id]);

            }
            else{
                $output['message'] = $this->getFormErrors($form);

            }



        }

        $output['lecture']= $lecture;
        $output['customCrumbs'] = [
            $this->url()->fromRoute('home') =>__('Home'),
            $this->url()->fromRoute('admin/default')=>__('Dashboard'),
            $this->url()->fromRoute('admin/default',['controller'=>'lesson','action'=>'index'])=>__('Classes'),
            $this->url()->fromRoute('admin/default',['controller'=>'lecture','action'=>'index','id'=>$lecture->lesson->lesson_id])=>__('Class Lectures'),
            $this->url()->fromRoute('admin/default',['controller'=>'lecture','action'=>'content','id'=>$lecture->lecture_id])=>'Lecture Content',
            '#'=>'Import PowerPoint File'
        ];
        $output['pageTitle'] = 'Import Powerpoint Slide to lecture: '.$lecture->lecture_title;
        $output['form'] = $form;
        return $output;
    }



    public function isImage($file)
    {
        if(@!is_array(getimagesize($file))){
            $image = false;
        }
        else {
            $image = true;
        }
        return $image;
    }

    private function getPdfForm(){
        $form = new BaseForm();
        $form->createHidden('path');
        $form->get('path')->setAttribute('id','path');
        $form->createText('title','Content Title',true);
        $form->createText('start','Start',false,'form-control number');
        $form->createText('end','End',false,'form-control number');
        $form->createText('pages','Pages',false,null,null,'e.g. 1,2,5,8,11');
        $form->createSelect('type','Pages to Import',['all'=>'All Pages','range'=>'Range','choose'=>'Choose Pages'],true);
        $form->setInputFilter($this->getPdfFilter());
        return $form;
    }

    private function getPdfFilter(){
        $filter = new InputFilter();
        $filter->add([
            'name'=>'title',
            'required'=>true,
            'validators'=>[
                [
                    'name'=>'NotEmpty'
                ]
            ]
        ]);

        $filter->add([
            'name'=>'path',
            'required'=>true,
            'validators'=>[
                [
                    'name'=>'NotEmpty'
                ]
            ]
        ]);

        $filter->add([
            'name'=>'type',
            'required'=>true,
            'validators'=>[
                [
                    'name'=>'NotEmpty'
                ]
            ]
        ]);

        $filter->add([
            'name'=>'start',
            'required'=>false,
            'validators'=>[
                [
                    'name'=>'Digits'
                ]
            ]
        ]);

        $filter->add([
            'name'=>'end',
            'required'=>false,
            'validators'=>[
                [
                    'name'=>'Digits'
                ]
            ]
        ]);

        $filter->add([
            'name'=>'pages',
            'required'=>false,
        ]);
        return $filter;
    }


    private function getZoomForm(){
        $form = new BaseForm();
        $form->createText('title','Title',true);
        $form->createText('meeting_id','Meeting ID');
        $form->createText('password','Meeting Password');
        $form->createTextArea('instructions','Instructions');
        $form->createText('sort_order','Sort Order');
        $form->setInputFilter($this->getZoomFilter());
        return $form;
    }

    private function getZoomFilter(){
        $filter = new InputFilter();
        $filter->add([
            'name'=>'title',
            'required'=>true,
            'validators'=>[
                [
                    'name'=>'NotEmpty'
                ]
            ]
        ]);

        $filter->add([
            'name'=>'meeting_id',
            'required'=>true,
            'validators'=>[
                [
                    'name'=>'NotEmpty'
                ]
            ]
        ]);

        $filter->add([
            'name'=>'password',
            'required'=>true,
            'validators'=>[
                [
                    'name'=>'NotEmpty'
                ]
            ]
        ]);


        return $filter;
    }


    private function getPptForm(){
        $form = new BaseForm();
        $form->createHidden('path');
        $form->get('path')->setAttribute('id','path');
        $form->createText('title','Content Title',true);
       $form->setInputFilter($this->getPptFilter());
        return $form;
    }

    private function getPptFilter(){
        $filter = new InputFilter();
        $filter->add([
            'name'=>'title',
            'required'=>true,
            'validators'=>[
                [
                    'name'=>'NotEmpty'
                ]
            ]
        ]);

        $filter->add([
            'name'=>'path',
            'required'=>true,
            'validators'=>[
                [
                    'name'=>'NotEmpty'
                ]
            ]
        ]);


        return $filter;
    }

    public function deletecontentAction()
    {
        $table = new LecturePageTable($this->getServiceLocator());
        $id = $this->params('id');
        $row = $table->getRecord($id);
        $lessonId = $row->lecture_id;
        try{
            $table->deleteRecord($id);
            $table->arrangeSortOrders($lessonId);
            $this->flashmessenger()->addMessage(__('Record deleted'));
        }
        catch(\Exception $ex){
            $this->deleteError();
        }

        $this->goBack();
    }


    private function getContentForm(){
        $form = new BaseForm();
        $form->createText('title','Title',true);
        $form->createTextArea('content','Content',false);
        $form->createText('sort_order','Sort Order',false,'form-control number');
        $form->createSelect('type','Content Type',['t'=>__('Text'),'v'=>__('Video'),'l'=>__('Video'),'c'=>__('Html Code'),'i'=>__('Image'),'z'=>__('zoom-meeting')],true);

        return $form;
    }

    private function getContentFilter(){
        $filter = new InputFilter();

        $filter->add([
            'name'=>'title',
            'required'=>true,
            'validators'=>[
                [
                    'name'=>'NotEmpty'
                ]
            ]
        ]);

        $filter->add([
            'name'=>'sort_order',
            'required'=>false,
            'validators'=>array(
                array(
                    'name'=>'Digits'
                )
            )
        ]);

        $filter->add([
            'name'=>'type',
            'required'=>true,
            'validators'=>[
                [
                    'name'=>'NotEmpty'
                ]
            ]
        ]);

        $filter->add([
            'name'=>'content',
            'required'=>false,

        ]);

        return $filter;

    }





    private function formatPdfPage($html){

        $object = \phpQuery::newDocumentHTML($html);

        $object->find('img')->removeAttr('width');
        $object->find('img')->removeAttr('height');
        $object->find('img')->attr('style','max-width:100%');
        $object->find('div')->removeAttr('id');
        $object->find('div')->removeAttr('style');
        //$object->find('div')->attr('style','position:relative; max-width:100%');
        $object->find('p')->removeAttr('style');

        //get the image name
        $url = $object->find('img')->attr('src');
        if(!empty($url)){
            $fileName = IMAGE_PATH.basename($url);

            $this->images[] = $fileName;
        }

        $returnContent = $object->html();

        return $returnContent;
    }

    private function cropImages(){
        foreach($this->images as $fileName){
            $original_img = imagecreatefrompng($fileName);
            unlink($fileName);
            $cropped_img_white = imagecropauto($original_img , IMG_CROP_THRESHOLD, null, 16777215);

            imagepng($cropped_img_white,$fileName,1);
            /*
            if($this->count==2){
                header('Content-Type: image/png');
                imagepng($cropped_img_white);
            }
            */

            imagedestroy($cropped_img_white);
            imagedestroy($original_img);
        }
        $fullPath = IMAGE_PATH ;
        array_map('unlink', glob( "$fullPath*.html"));
    }

    private function delete_recursively_($path,$match){
        static $deleted = 0,
        $deleted_size = 0;
        $dirs = glob($path."*");
        $files = glob($path.$match);
        foreach($files as $file){
            if(is_file($file)){
                $deleted_size += filesize($file);
                unlink($file);
                $deleted++;
            }
        }
        foreach($dirs as $dir){
            if(is_dir($dir)){
                $dir = basename($dir) . "/";
                delete_recursively_($path.$dir,$match);
            }
        }
        return __('lecture-files-del-msg',['deleted'=>$deleted,'deleted_size'=>$deleted_size]);

    }

    public function addquizAction(){

        $id = $this->params('id');
        $table = new LecturePageTable($this->getServiceLocator());
        if($this->request->isPost()){

            $data = $this->request->getPost();
            if(!empty($data['name'])){

                //create the json

                $info = new \stdClass();
                $info->name = $data['name'];
                $info->main =  $data['main'];
                $info->results = __('quiz-thanks');
                $info->level1 = __('Excellent');
                $info->level2 = __('Good');
                $info->level3 = __('Average');
                $info->level4 = __('Below Average');
                $info->level5 = __('Poor');


                $obj = new \stdClass();
                $obj->json = new \stdClass();
                $obj->json->info = $info;
                $obj->json->questions = [];
                $obj->checkAnswerText = __('Check My Answer!');
                $obj->nextQuestionText = __('Next').' &raquo;';
                $obj->backButtonText = '';
                $obj->completeQuizText = '';
                $obj->tryAgainText = '';
                $obj->questionCountText = __('Question').' %current '.__('of').' %total';
                $obj->preventUnansweredText = __('you-must-select');
                $obj->questionTemplateText=  '%count. %text';
                $obj->scoreTemplateText= '%score / %total';
                $obj->nameTemplateText=  '<span>'.__('Quiz').': </span>%name';
                $obj->skipStartButton= false;
                $obj->numberOfQuestions= null;
                $obj->randomSortQuestions= false;
                $obj->randomSortAnswers= false;
                $obj->preventUnanswered= false;
                $obj->disableScore= false;
                $obj->disableRanking= false;
                $obj->scoreAsPercentage= false;
                $obj->perQuestionResponseMessaging= true;
                $obj->perQuestionResponseAnswers= false;
                $obj->completionResponseMessaging= false;
                $obj->displayQuestionCount= true;
                $obj->displayQuestionNumber= true;




                $json = json_encode($obj);

                $lecturePage = new LecturePage();
                $lecturePage->title = $data['name'];
                $lecturePage->lecture_id = $id;
                $lecturePage->type = 'q';
                $lecturePage->sort_order = $table->getNextSortOrder($id);
                $lecturePage->content = $json;
                $lecturePage->save();

                //now forward to editing page
                return $this->redirect()->toRoute('admin/default',['controller'=>'lecture','action'=>'editquiz','id'=>$lecturePage->lecture_page_id]);
            }



        }

        return $this->goBack();


    }

    public function editquizAction(){

        $id = $this->params('id');
        $table = new LecturePageTable($this->getServiceLocator());
        $lecturePage = LecturePage::find($id);
        //dd(json_decode($lecturePage->content) );
        if($this->request->isPost()){

            $info = new \stdClass();


            $obj = new \stdClass();
            $obj->json = new \stdClass();
            $obj->json->info = $info;

            //dd($this->request->getPost('content'));
            $quizPost = $this->request->getPost('content');
            foreach($quizPost as $key=>$value){
                $obj->$key = booleanValue($value);
            }

            unset($obj->json['questions']);
            $obj->json['questions'] = [];
            if(isset($quizPost['json']['questions'])){
                foreach($quizPost['json']['questions'] as $question){
                    $questionObject = new \stdClass();
                    $questionObject->q = $question['q'];
                    $questionObject->correct = $question['correct'];
                    $questionObject->incorrect = $question['incorrect'];
                    if(isset($question['select_any'])){
                        $questionObject->select_any = booleanValue($question['select_any']);
                    }

                    if(isset($question['force_checkbox'])){
                        $questionObject->force_checkbox = booleanValue($question['force_checkbox']);
                    }

                    if(isset($question['a'])){
                        foreach($question['a'] as $option){
                            $optionObs = new \stdClass();
                            $optionObs->option= $option['option'];
                            $optionObs->correct = booleanValue(@$option['correct']);
                            $questionObject->a[] = $optionObs;
                        }
                    }


                    $obj->json['questions'][] = $questionObject;
                }
            }



            $lecturePage->content = json_encode($obj);
            $lecturePage->sort_order = $this->request->getPost('sort_order');
            $lecturePage->title = $this->request->getPost('title');
            $lecturePage->save();

            $table->arrangeSortOrders($lecturePage->lecture_id);
            exit('true');
        }


        return [
            'pageTitle'=>__('Edit Quiz').': '.$lecturePage->title,
            'lecturePage'=>$lecturePage,
            'customCrumbs' => [
                $this->url()->fromRoute('home') =>__('Home'),
                $this->url()->fromRoute('admin/default')=>__('Dashboard'),
                $this->url()->fromRoute('admin/default',['controller'=>'lesson','action'=>'index'])=>__('Classes'),
                $this->url()->fromRoute('admin/default',['controller'=>'lecture','action'=>'index','id'=>$lecturePage->lecture->lesson->lesson_id])=>__('Class Lectures'),
                $this->url()->fromRoute('admin/default',['controller'=>'lecture','action'=>'content','id'=>$lecturePage->lecture->lecture_id])=>__('Lecture Content'),
                '#'=>__('Edit Quiz')
            ]
        ];
    }

    public function addaudioAction(){

        if($this->request->isPost()){

            $id = $this->request->getPost('id');
            $lecturePage = LecturePage::find($id);
            $url = $this->request->getPost('url');
            $url = trim($url);
            if(!isUrl($url)){
                $this->flashMessenger()->addMessage(__('Invalid url'));
                return $this->goBack();
            }
            $getValues=file_get_contents('http://soundcloud.com/oembed?format=js&url='.$url.'&iframe=true');
//Clean the Json to decode
            $decodeiFrame=substr($getValues, 1, -2);
//json decode to convert it as an array
            $jsonObj = json_decode($decodeiFrame);

//Change the height of the embed player if you want else uncomment below line
// echo $jsonObj->html;

            $code = str_replace('height="400"', 'height="140"', $jsonObj->html);
            if(!empty($code)){
                $lecturePage->audio_code = $code;
                $lecturePage->save();
                $this->flashMessenger()->addMessage(__('Audio added successfully'));
            }
            else{
                $this->flashMessenger()->addMessage(__('unable-to-add-audio'));
            }


        }

        return $this->goBack();
    }

    public function removeaudioAction(){
        $id = $this->params('id');
        $lecturePage = LecturePage::find($id);
        $lecturePage->audio_code = null;
        $lecturePage->save();
        $this->flashMessenger()->addMessage(__('Audio removed'));
        return $this->goBack();
    }

    public function libraryAction(){

        //forward to controller

        $viewModel = $this->forward()->dispatch('Admin\Controller\Video',['action'=>'index']);
        $data = $viewModel->getVariables();
        $data['paginator']->setItemCountPerPage(10);

        $data['lectureId']= $this->params('id');
        $vModel = new ViewModel($data);
        $vModel->setTerminal(true);
        return $vModel;
    }

    public function addvideolibraryAction(){
        $table = new LecturePageTable($this->getServiceLocator());
        $videoId = $this->params('id');
        $lectureId = $this->params()->fromQuery('lecture');

        //check if there is another video in library
        $count = LecturePage::where('lecture_id',$lectureId)->where('type','l')->count();
        if($count>0){
            $this->flashMessenger()->addMessage('For security purposes, you can not add more than one library video to a lecture. If you wish to add more videos to this class, create a new lecture and add the video there.');

        return $this->redirect()->toRoute('admin/default',['controller'=>'lecture','action'=>'content','id'=>$lectureId]);
        //return $this->goBack();
        }


        $video= Video::find($videoId);
        $data = [];
        $data['lecture_id'] = $lectureId;


        $data['sort_order'] = $table->getNextSortOrder($lectureId);

        $data['title'] = $video->name;
        $data['content']= $videoId;
        $data['type']='l';

        $table->addRecord($data);
        $table->arrangeSortOrders($lectureId);
        $this->flashMessenger()->addMessage('Video added');
        return $this->goBack();

    }

    public function importimagesAction(){

        $id = $this->params('id');

        $user= '';
        if(defined('USER_ID')){
            $user = '/'.USER_ID;
        }
        $filePath = 'public/usermedia'.$user;
        $userBaseUrl = 'usermedia'.$user;


        $table = new LecturePageTable();
        $lecture = Lecture::find($id);

        if($this->request->isPost()){
            $form = $this->getUploadForm();
            $formData = $this->request->getPost();

            $form->setData(array_merge_recursive(
                $formData->toArray(),
                $this->request->getFiles()->toArray()
            ));
            $file =  $_FILES['files'];

            if(!$form->isValid()){
                $json = json_encode([
                    'files'=>[
                        [
                            'name'=>$file['name'][0],
                            'size'=>$file['size'][0],
                            'error'=>__('Invalid upload')
                        ]
                    ]
                ]);
                exit($json);
            }


            //get file name
            $name = $file['name'][0];
            $tmpName = $file['tmp_name'][0];


            $time = time();
            $safeName= safeUrl($name);
         //   $safeName= substr($safeName,0,15);
            $time = $id;

            if(!$this->hasPermission('misc/global_access')){

                if(!is_dir(USER_PATH.'/admin_files')){
                    mkdir(USER_PATH.'/admin_files') or die('unable to make folder');
                }

                if(!is_dir(USER_PATH.'/admin_files/'.$this->getAdminId())){
                    mkdir(USER_PATH.'/admin_files/'.$this->getAdminId()) or die('unable to make user folder');
                }


                $basePath = USER_PATH.'/admin_files/'.$this->getAdminId().'/image_import';
                if(!is_dir($basePath)){
                    mkdir($basePath) or die('unable to create subdirectory: '.$basePath);
                }

                $basePath = $basePath. '/'.date('Y_m_d');
                if(!is_dir($basePath)){
                    mkdir($basePath) or die('unable to create subdirectory: '.$basePath);
                }

                $pptFolder = $basePath.'/'.$time;

                if(!is_dir($pptFolder)){
                    mkdir($pptFolder) or die('unable to create subdirectory: '.$pptFolder);
                }

                $fileUrl = $userBaseUrl.'/admin_files/'.$this->getAdminId().'/image_import/'.date('Y_m_d').'/'.$time;
                define('BASE_URL',$fileUrl);

            }else{
                $basePath= USER_PATH.'/image_import';
                if(!is_dir($basePath)){
                    mkdir($basePath) or die('unable to create ppt directory');
                    //  chmod($basePath, 0777);
                }

                $basePath = $basePath. '/'.date('Y_m_d');
                if(!is_dir($basePath)){
                    mkdir($basePath) or die('unable to create subdirectory: '.$basePath);
                    //  chmod($basePath, 0777);
                }


                $pptFolder = $basePath.'/'.$time;

                if(!is_dir($pptFolder)){
                    mkdir($pptFolder) or die('unable to create subdirectory: '.$pptFolder);
                    //   chmod($pptFolder, 0777);
                }


                $fileUrl = $userBaseUrl.'/image_import/'.date('Y_m_d').'/'.$time;
                define('BASE_URL',$fileUrl);
            }


            $basePath = realpath($pptFolder);

            define('BASE_PATH',$basePath);

            $path_parts = pathinfo($name);

            $newName = substr($path_parts['filename'],0,15).'_'.time();
            $safeName = safeUrl($newName).'.'.$path_parts['extension'];
            //move image to new path
            rename($tmpName,BASE_PATH.'/'.$safeName);
            @chmod(BASE_PATH.'/'.$safeName,0644);



            $lecturePage = new LecturePage();
            $lecturePage->sort_order = $table->getNextSortOrder($id);
            $lecturePage->lecture_id =$id;
            $lecturePage->title = $path_parts['filename'];
            $lecturePage->content = BASE_URL.'/'.$safeName ;
            $lecturePage->type= 'i';
            $lecturePage->save();




            $json = json_encode([
                'files'=>[
                    [
                        'name'=>__('File Upload Successful').': '.$file['name'][0],
                        'size'=>$file['size'][0],
                        'thumbnailUrl'=>$this->getBaseUrl().'/img/success.png'
                    ]
                ]
            ]);


            exit($json);

        }


        $output['lecture']= $lecture;
        $output['customCrumbs'] = [
            $this->url()->fromRoute('home') =>__('Home'),
            $this->url()->fromRoute('admin/default')=>__('Dashboard'),
            $this->url()->fromRoute('admin/default',['controller'=>'lesson','action'=>'index'])=>__('Classes'),
            $this->url()->fromRoute('admin/default',['controller'=>'lecture','action'=>'index','id'=>$lecture->lesson->lesson_id])=>__('Class Lectures'),
            $this->url()->fromRoute('admin/default',['controller'=>'lecture','action'=>'content','id'=>$lecture->lecture_id])=>__('Lecture Content'),
            '#'=>__('Import Images')
        ];
        $output['pageTitle'] = __('Import Images to lecture').': '.$lecture->lecture_title;

        return $output;

    }


    private function getUploadForm(){

        $form = new BaseForm();

        $file = new File('files');
        $file->setLabel(__('Your File'))
            ->setAttribute('id','file_path')
            ->setAttribute('required','required');
        $form->add($file);
        $form->setInputFilter($this->getUploadFilter());
        return $form;

    }

    private function getUploadFilter(){

        $filter = new InputFilter();



        $input = new Input('files');
        $input->setRequired(true);
        $input->getValidatorChain()
            ->attach(new Size(209715200))
            ->attach(new Extension('jpg,jpeg,png,gif'));

        $filter->add($input);



        return $filter;
    }


}