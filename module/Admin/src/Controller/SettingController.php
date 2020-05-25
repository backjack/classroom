<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 1/12/2017
 * Time: 4:34 PM
 */

namespace Admin\Controller;


use Application\Controller\AbstractController;
use Application\Entity\Country;
use Application\Entity\Currency;
use Application\Entity\TestGrade;
use Application\Form\AccountFilter;
use Application\Form\AccountForm;
use Application\Form\FieldFilter;
use Application\Form\FieldForm;
use Application\Form\RoleFilter;
use Application\Form\RoleForm;
use Application\Form\SettingForm;
use Application\Model\AccountsTable;
use Application\Model\PermissionGroupTable;
use Application\Model\PermissionTable;
use Application\Model\RegistrationFieldTable;
use Application\Model\RolePermissionTable;
use Application\Model\RoleTable;
use Application\Model\SettingTable;
use Intermatics\BaseForm;
use Intermatics\Files;
use Intermatics\HelperTrait;
use Phinx\Console\PhinxApplication;
use Phinx\Wrapper\TextWrapper;
use Zend\Form\Element\Select;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Intermatics\UtilityFunctions;

define('DIR_MER_IMAGE', 'public/');
class SettingController extends AbstractController {

    use HelperTrait;

    public function indexAction()
    {
        $output = [];
        $settingTable = new SettingTable($this->getServiceLocator());
        $settingForm = new SettingForm(null,$this->getServiceLocator());
        $rowset = $settingTable->getRecords();
        $rowset->buffer();
        if($this->request->isPost())
        {

           unset($_SESSION['style']);
                $formData = $this->request->getPost();
                foreach($rowset as $row){
                    $settingTable->saveSetting($row->key,$formData[$row->key]);
                }
            $output['message']=__('Changes Saved!');
            $settingForm->setData($formData);

            if(isset($_SESSION['currency'])){
                unset($_SESSION['currency']);
            }

            //update default currency
            $countryId = $formData['country_id'];
            //check if country exists
            $currency = Currency::where('country_id',$countryId)->first();
            if(!$currency){
                $currency = new Currency();
                $currency->country_id= $countryId;
            }
            $currency->exchange_rate = 1;
            $currency->save();

        }
        else{
            $data = [];
            foreach($rowset as $row){
                $data[$row->key] = $row->value;
            }
            $settingForm->setData($data);

            $output['no_image']= resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
            $output['display_image']= resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
        }

        $logo = $settingTable->getSetting('image_logo');
        if(!empty($logo)){
            $output['logo']= resizeImage($logo, 100, 100,$this->getBaseUrl());
        }
        else{
            $output['logo']= resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
        }

        $icon = $settingTable->getSetting('image_icon');
        if(!empty($icon)){
            $output['icon'] =  resizeImage($icon, 100, 100,$this->getBaseUrl());

        }
        else{
            $output['icon']= resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
        }
        $output['no_image']= resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
        $output['settings'] = $rowset;
        $output['form']=$settingForm;
        $output['pageTitle'] = __('Site Settings');
        $output['siteUrl'] = $this->getBaseUrl();
        return $output;

    }




    public function fieldsAction(){

        $table = new RegistrationFieldTable($this->getServiceLocator());

        $paginator = $table->getPaginatedRecords(true);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        return new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Custom Student Fields'),
        ));

    }


    public function addfieldAction(){

        $output = array();
        $table = new RegistrationFieldTable($this->getServiceLocator());
        $form = new FieldForm();
        $filter = new FieldFilter();

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
                $form = new FieldForm(null);
                $this->flashMessenger()->addMessage(__('Field added'));
                return $this->redirect()->toRoute('admin/default',['controller'=>'setting','action'=>'fields']);
            }
            else{
                $output['message'] = __('save-failed-msg');

            }

        }

        $output['form'] = $form;
        $output['pageTitle']= __('Add Field');
        $output['action']='addfield';
        $output['id']=null;
        return new ViewModel($output);


    }

    public function deletefieldAction(){
        $table = new RegistrationFieldTable($this->getServiceLocator());
        $id = $this->params('id');
        $table->deleteRecord($id);
        $this->flashmessenger()->addMessage(__('Record deleted'));
        $this->redirect()->toRoute('admin/default',array('controller'=>'setting','action'=>'fields'));

    }

    public function editfieldAction(){


        $output = array();
        $table = new RegistrationFieldTable($this->getServiceLocator());
        $form = new FieldForm(null);
        $filter = new FieldFilter();
        $id = $this->params('id');

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
                $row = $table->getRecord($id);
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
        $output['pageTitle']= __('Edit Field');
        $output['row']= $row;
        $output['action']='editfield';

        $viewModel = new ViewModel($output);
        $viewModel->setTemplate('admin/setting/addfield');
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

    public function rolesAction(){
        $table = new RoleTable($this->getServiceLocator());

        $paginator = $table->getPaginatedRecords(true);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        return new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Roles'),
        ));
    }

    public function addroleAction(){
        $roleTable = new RoleTable($this->getServiceLocator());
        $permissionTable = new PermissionTable($this->getServiceLocator());
        $rolePermissionTable = new RolePermissionTable($this->getServiceLocator());
        $groups = new PermissionGroupTable($this->getServiceLocator());
        $form = new RoleForm(null,$this->getServiceLocator());
        $filter = new RoleFilter();
        $form->setInputFilter($filter);
        $output =[];
        if($this->request->isPost())
        {
            $formData = $this->request->getPost();
            $form->setData($formData);

            if($form->isValid()){
                $data = $form->getData();

                $id = $roleTable->addRecord(['role'=>$data['role']]);

                $permissions = $permissionTable->getRecords();
                foreach($permissions as $row){
                    $key = 'permission_'.strtolower(str_replace(' ','_',$row->group)).'_'.$row->permission_id;
                    if(!empty($data[$key]))
                    {
                        $rolePermissionTable->addRecord([
                           'role_id'=>$id,
                            'permission_id'=>$data[$key]
                        ]);

                    }
                }

                $this->flashMessenger()->addMessage(__('Role created'));
                $this->redirect()->toRoute('admin/default',['controller'=>'Setting','action'=>'roles']);
            }
            else{
                $output['message'] = 'Save failed. Please enter a role name';
            }


        }

        $output['form'] = $form;
        $output['groups'] = $groups->getRecords();
        $output['action']='add';
        $output['id']=null;
        $output['pageTitle']=__('Add Role');
        return $output;
    }


    public function editroleAction(){

        $roleTable = new RoleTable($this->getServiceLocator());
        $permissionTable = new PermissionTable($this->getServiceLocator());
        $rolePermissionTable = new RolePermissionTable($this->getServiceLocator());
        $groups = new PermissionGroupTable($this->getServiceLocator());
        $form = new RoleForm(null,$this->getServiceLocator());
        $filter = new RoleFilter();
        $form->setInputFilter($filter);
        $output =[];
        $id = $this->params('id');
        if($id==1){
            $this->flashMessenger()->addMessage(__('no-role-edit'));
            $this->goBack();
        }
        if($this->request->isPost())
        {

            $formData = $this->request->getPost();
            $form->setData($formData);

            if($form->isValid()){
                $data = $form->getData();

                $roleTable->update(['role'=>$data['role']],$id);
                $rolePermissionTable->deletePermissionsForRole($id);
                $permissions = $permissionTable->getRecords();
                foreach($permissions as $row){
                    $key = 'permission_'.strtolower(str_replace(' ','_',$row->group)).'_'.$row->permission_id;
                    if(!empty($data[$key]))
                    {
                        $rolePermissionTable->addRecord([
                            'role_id'=>$id,
                            'permission_id'=>$data[$key]
                        ]);

                    }
                }

                $this->flashMessenger()->addMessage(__('Role edited'));
                $this->redirect()->toRoute('admin/default',['controller'=>'setting','action'=>'roles']);
            }
            else{
                $output['message'] = __('role-save-error');
            }


        }
        else{

            $role = $roleTable->getRecord($id);
            $data = ['role'=>$role->role];
            $rowset = $rolePermissionTable->getPermissionsForRole($id);
            foreach($rowset as $row){

                $data['permission_'.strtolower(str_replace(' ','_',$row->group)).'_'.$row->permission_id]=$row->permission_id;

            }


            $form->setData($data);
        }

        $output['form'] = $form;
        $output['groups'] = $groups->getRecords();
        $output['action']='edit';
        $output['id']= $id;
        $output['pageTitle']=__('Edit Role');
        $viewModel = new ViewModel($output);
        $viewModel->setTemplate('admin/setting/addrole.phtml');
        return $viewModel;
    }

    public function deleteaccountAction(){
        try{

            $id = $this->params('id');
            $table = new AccountsTable($this->getServiceLocator());
            $admin = $this->getAdmin();
            $row = $table->getRecord($id);
            if($row->email==$admin->email){
                $this->flashMessenger()->addMessage(__('account-delete-error'));
                return   $this->redirect()->toRoute('admin/default',['controller'=>'setting','action'=>'admins']);
            }

            $table->deleteRecord($id);

        }
        catch(\Exception $ex){
            $this->deleteError();
        }
        $this->redirect()->toRoute('admin/default',['controller'=>'setting','action'=>'admins']);
    }



    public function deleteroleAction(){
        try{

            $id = $this->params('id');
            if($id < 4){
                $this->flashMessenger()->addMessage(__('no-role-delete'));
                return $this->goBack();
            }
            $roleTable = new RoleTable($this->getServiceLocator());
            $roleTable->deleteRecord($id);

        }
        catch(\Exception $ex){
            $this->deleteError();
        }
        $this->redirect()->toRoute('admin/default',['controller'=>'setting','action'=>'roles']);
    }

    public function adminsAction()
    {
        $table = new AccountsTable($this->getServiceLocator());

        $paginator = $table->getPaginatedRecords(true);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        return new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Administrators'),
        ));
    }

    public function addadminAction(){

        if(!$this->canAddAdmin()){
            $this->redirect()->toRoute('admin/default',['controller'=>'setting','action'=>'admins']);
        }

        $accountsTable = new AccountsTable($this->getServiceLocator());
        $form = new AccountForm(null,$this->getServiceLocator());
        $filter = new AccountFilter();
        $form->setInputFilter($filter);
        $output = [];
        if($this->request->isPost())
        {
            $formData = $this->request->getPost();
            $form->setData($formData);
            if($form->isValid())
            {
                $data = $form->getData();
                $password = $data['password'];
                $data['password'] = md5($data['password']);
                unset($data['confirm_password']);
                $email = $data['email'];
                $url = $this->getBaseUrl().'/admin';
                $message = __("new-admin-mail",['email'=>$email,'password',$password,'url'=>$url]);

                try{
                $accountsTable->addRecord($data);
                if(!empty($formData['senddetails'])){
                    $this->sendEmail($email,__('New Account'),$message);
                }

                $this->flashMessenger()->addMessage('account created!');
                $this->redirect()->toRoute('admin/default',['controller'=>'setting','action'=>'admins']);
                }
                catch(\Exception $ex){
                    $output['message'] = __('account-creation-error');
                }

            }
            else{

                $formMessages = $form->getMessages();

                $output['message']=__('save-failed-msg');
                if ($formData['picture']) {
                    $output['display_image']= resizeImage($formData['picture'], 100, 100,$this->getBaseUrl());
                }
            }

        }
        else{
            $output['no_image']= resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
            $output['display_image']= resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());

        }

        $output['pageTitle'] = __('Add Administrator');
        $output['form'] = $form;
        $output['action'] = 'add';
        $output['id']=null;
        return $output;
    }

    public function getAuthService()
    {

        return $this->getServiceLocator()->get('AdminAuthService');
    }


    public function editadminAction(){
        $accountsTable = new AccountsTable($this->getServiceLocator());
        $form = new AccountForm(null,$this->getServiceLocator());
        $filter = new AccountFilter();

        $filter->remove('password');
        $filter->remove('confirm_password');

        $form->setInputFilter($filter);
        $output = [];
        $id = $this->params('id');
        $row = $accountsTable->getRecord($id);

        $form->get('password')->setAttribute('required','');
        $form->get('confirm_password')->setAttribute('required','');

        $form->get('password')->setAttribute('placeholder',__('Optional'));
        $form->get('confirm_password')->setAttribute('placeholder',__('Optional'));

        if($this->request->isPost())
        {
            $formData = $this->request->getPost();
            $form->setData($formData);
            if($form->isValid())
            {
                //get current admin
                $admin = $this->getAdmin();

                $data = $form->getData();

                if($id==$admin->account_id && $admin->email != $data['email']){
                    $this->getAuthService()->getStorage()->write(array(
                        'email'=>$data['email'],
                        'role'=>'admin'
                    ));
                }

                $password = $data['password'];
                if(!empty($password)){
                    $data['password'] = md5($data['password']);
                    unset($data['confirm_password']);
                    $email = $data['email'];

                    $message = __('admin-password-reset-msg',['password'=>$password]);
                }
                else{
                    unset($data['confirm_password'],$data['password']);
                }
                $accountsTable->update($data,$id);
                try{

                    $siteName = $this->getSetting('general_site_name',$this->getServiceLocator());
                    if(!empty($formData['senddetails']) && $row->email != $email){
                        $this->sendEmail($email,__('Account Modified'),$message);
                    }
                }
                catch(\Exception $ex){

                    $output['message'] =  __('account-creation-error');
                }

                $this->flashMessenger()->addMessage(__('Changes Saved!'));
                $this->redirect()->toRoute('admin/default',['controller'=>'setting','action'=>'admins']);


            }
            else{

                $formMessages = $form->getMessages();

                $output['message']=__('save-failed-msg');
                //   print_r($form->e)
            }

        }
        else{
            $formData = UtilityFunctions::getObjectProperties($row);
            unset($formData['password']);
            $form->setData($formData);
        }


        if ($row->picture && file_exists(DIR_MER_IMAGE . $row->picture) && is_file(DIR_MER_IMAGE . $row->picture)) {
            $output['display_image'] = resizeImage($row->picture, 100, 100,$this->getBaseUrl());
        } else {
            $output['display_image'] = resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());
        }


        $output['no_image']= $this->getBaseUrl().'/'.resizeImage('img/no_image.jpg', 100, 100,$this->getBaseUrl());


        $output['pageTitle'] = __('Edit Administrator');
        $output['form'] = $form;
        $output['action'] = 'edit';
        $output['id']=$id;
        $viewModel = new ViewModel($output);
        $viewModel->setTemplate('admin/setting/addadmin.phtml');
        return $viewModel;
    }



    public function migrateAction(){

        $output = [];
        $output['pageTitle'] = __('Update');
        if($this->request->isPost()){
            $file = 'update/app.zip';
            if(!file_exists($file)){

                $this->flashMessenger()->addMessage(__('no-update-file'));
                return $this->goBack();
            }

            $files= new Files();


            //create temp directory
            $tmpDir = 'update_tmp';
            if(!file_exists($tmpDir)){
                mkdir($tmpDir);
            }

            $userPath = $tmpDir.'/usermedia';
            $configFile = $tmpDir.'/local.php';
            $files->delete($userPath);
            $files->delete($configFile);


            //copy user media and config file to this directory

            copy('config/autoload/local.php',$configFile);


            $this->recurseCopy('public/usermedia',$userPath);
//            $files->copyOrMove('public/usermedia',$userPath,'','move');
            //now move app.zip to root
            copy('update/app.zip','./app.zip');

            //now unzip file
            $zip = new \ZipArchive();
            $res = $zip->open('./app.zip');
            if ($res === TRUE) {
                $zip->extractTo('./');
                $zip->close();
            } else {
                $files->delete($userPath);
                $files->delete($configFile);

                @unlink('app.zip');
                $this->flashMessenger()->addMessage(__('zip-error'));
                return $this->goBack();
            }

            //copy usermedia and config file back
            $files->delete('config/autoload/local.php');
            copy($configFile,'config/autoload/local.php');

            $files->delete('public/usermedia');
            $this->recurseCopy($userPath,'public/usermedia');
         //   $files->copyOrMove($userPath,'public/usermedia','','move');

            //delete temp files
          //  $files->delete($userPath);
            $files->delete($configFile);
            $files->delete($file);
            $files->delete($tmpDir);
            $files->delete('app.zip');


            //run migrations
            $config = $this->getServiceLocator()->get('config');
            $dbParams = $config['db'];
            $dbParams['database'] = $dbParams['dbname'];
            $GLOBALS['dbParams'] = $dbParams;

            $phinxApp = new PhinxApplication();
            $phinxTextWrapper = new TextWrapper($phinxApp);
            $path = 'vendor/bin/phinx-web.php';

            $phinxTextWrapper->setOption('configuration',$path);
            $phinxTextWrapper->setOption('parser','PHP');
            $phinxTextWrapper->setOption('environment', 'development');
            $log = $phinxTextWrapper->getMigrate();
            $output['log'] = $log;

            $this->flashMessenger()->addMessage(__('update-complete'));
            return $this->redirect()->toRoute('admin/default',['controller'=>'setting','action'=>'migrate']);

        }

        //check for file
        if(file_exists('update/app.zip')){
            $output['file'] = true;
        }
        else{
            $output['file']=false;
        }




        return $output;
    }

    private function recurseCopy($src, $dst)
    {
        $dir = opendir($src);
        mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    $this->recurseCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    public function testgradesAction(){

        $this->data['grades'] = TestGrade::orderBy('grade','asc')->get();
        $this->data['pageTitle'] = __('Grades');
        return $this->bladeView('admin.setting.testgrades',$this->data);
    }

    public function addtestgradeAction(){
        $form = $this->getTestGradeForm();

        if($this->request->isPost()){
            $formData = $this->request->getPost();
            $form->setData($formData);
            if($form->isValid()){
                TestGrade::create($form->getData());
                $this->flashMessenger()->addMessage('Grade created');
                return $this->redirect()->toRoute('admin/default',['controller'=>'setting','action'=>'testgrades']);
            }
            else{
                $this->data['message'] = $this->getFormErrors($form);
            }
        }
        $this->data['form'] = $form;
        $this->data['pageTitle'] = __('Add Test Grade');

        return $this->bladeView('admin.setting.gradeform',$this->data);
    }

    public function edittestgradeAction(){
        $form = $this->getTestGradeForm();
        $id= $this->params('id');
        $testGrade = TestGrade::find($id);
        if($this->request->isPost()){
            $formData = $this->request->getPost();
            $form->setData($formData);
            if($form->isValid()){
                $testGrade->fill($form->getData());
                $testGrade->save();
                $this->flashMessenger()->addMessage('Grade saved');
                return $this->redirect()->toRoute('admin/default',['controller'=>'setting','action'=>'testgrades']);
            }
            else{
                $this->data['message'] = $this->getFormErrors($form);
            }
        }
        else{
            $form->setData($testGrade->toArray());

        }

        $this->data['form'] = $form;
        $this->data['pageTitle'] = __('Edit Test Grade').': '.$testGrade->grade;


        return $this->bladeView('admin.setting.gradeform',$this->data);
    }

    public function deletetestgradeAction(){
        $id = $this->params('id');
        $testGrade = TestGrade::find($id);
        $testGrade->delete();
        $this->flashMessenger()->addMessage(__('Grade deleted'));
        return $this->goBack();
    }


    public function currenciesAction(){

        $currentCountry  = $this->getSetting('country_id');
        $currencies = Currency::orderBy('currency_id','desc')->paginate(30);
        $pageTitle = __('Currencies');
        $countries = Country::orderBy('currency_name')->groupBy('currency_code')->get();

        return $this->bladeView('admin.setting.currencies',compact('currentCountry','currencies','pageTitle','countries'));
    }

    public function addcurrencyAction(){
        if($this->request->isPost()){
            $country = $this->request->getPost('country');
            $countryModel= Country::find($country);
            if($countryModel && !Currency::where('country_id',$country)->first()){
                $currency = new Currency();
                $currency->country_id= $country;
                $currency->exchange_rate = $this->request->getPost('exchange_rate');
                $currency->save();
                $this->flashMessenger()->addMessage(__('currency-added'));
            }
        }
        return $this->goBack();
    }

    public function deletecurrencyAction(){
        $id  = $this->params('id');
        $currency= Currency::find($id);
        if($currency){
            $currency->delete();
            $this->flashMessenger()->addMessage(__('Currency removed'));
        }
        return $this->goBack();
    }

    public function updatecurrencyAction(){
        $id = $this->params('id');
        if($this->request->isPost()){
            $rate = $this->request->getPost('rate');
            $currency = Currency::find($id);
            $currency->exchange_rate = $rate;
            $currency->save();
        }
        exit('done');
    }


    public function languageAction(){
        $output=[];
        $select = new Select('language');
        $settingsTable = new SettingTable();
        $select->setAttribute('class','form-control');
        $options = include 'data/lang/list.php';
        $select->setValueOptions($options);
        $select->setLabel(__('language'));

        if($this->request->isPost()){
            $language = $this->request->getPost('language');
            if(!empty($language)){
                $settingsTable->saveSetting('config_language',$language);
            }
        }

        $lang = getSetting('config_language');
        if(empty($lang)){
            $lang = 'en';
        }

        $select->setValue($lang);
        $output['select'] = $select;
        $output['pageTitle']= __('language');
        return $output;
    }


    private function getTestGradeForm(){
        $form = new BaseForm();
        $form->createText('grade','Grade',true,null,null,'e.g. A');
        $form->createText('min','Minimum',true,'form-control number');
        $form->createText('max','Maximum',true,'form-control number');
        $form->setInputFilter($this->getTestGradeFilter());
        return $form;
    }

    private function getTestGradeFilter(){
        $filter = new InputFilter();
        $filter->add([
            'name'=>'grade',
            'required'=>'true',
            'validators'=>[
                [
                    'name'=>'NotEmpty'
                ]
            ]
        ]);

        $filter->add([
            'name'=>'min',
            'required'=>'true',
            'validators'=>[
                [
                    'name'=>'NotEmpty'
                ],
                [
                    'name'=>'Digits'
                ]
            ]
        ]);

        $filter->add([
            'name'=>'max',
            'required'=>'true',
            'validators'=>[
                [
                    'name'=>'NotEmpty'
                ],
                [
                    'name'=>'Digits'
                ]
            ]
        ]);
return $filter;


    }


}