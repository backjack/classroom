<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Entity\Country;
use Application\Entity\Newsflash;
use Application\Entity\PaymentMethodField;
use Application\Entity\Session;
use Application\Entity\Setting;
use Application\Entity\SmsGatewayField;
use Application\Entity\TemplateOption;
use Application\Entity\Video;
use Application\Model\LessonTable;
use Application\Model\NewsflashTable;
use Application\Model\SessionTable;
use Application\Model\SettingTable;
use Application\Model\StudentSessionTable;
use Application\Model\WidgetTable;
use Application\Model\WidgetValueTable;
use Intermatics\HelperTrait;
use Phinx\Console\PhinxApplication;
use Phinx\Wrapper\TextWrapper;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Application\Model\ArticlesTable;
use Zend\View\View;

class IndexController extends AbstractController
{
    use HelperTrait;

    /**
     * @return ViewModel
     * This is the method for the homepage. It loads all the configured widgets
     * into the view
     */
    public function indexAction()
    {
        $session = new \Zend\Session\Container('client');
        $session->type = 'desktop';


        $_GET['homepage'] = true;
        $widgetTable = new WidgetValueTable($this->getServiceLocator());

        $widgets = $widgetTable->getWidgets(1,'w');
        $sessionTable = new SessionTable($this->getServiceLocator());

        //get callendar
       $viewModel=  $this->forward()->dispatch('Application\Controller\Student',['action'=>'enroll','terminal'=>1]);
       $viewModel->setTerminal(true);

        $viewRender = $this->getServiceLocator()->get('ViewRenderer');
        $calendar = $viewRender->render($viewModel);

        //do blog
        $newsTable = new NewsflashTable($this->getServiceLocator());
        $_GET['hideTitle']=true;


        $viewModel=  $this->forward()->dispatch('Application\Controller\Login',['action'=>'registerwidget']);
       // $viewModel->setTemplate('login/register_widget');
        $viewModel->setTerminal(true);


        $viewRender = $this->getServiceLocator()->get('ViewRenderer');
        $regForm = $viewRender->render($viewModel);

        //$this->layout('layout/layout');
        return $this->getViewModel(['newsTable'=>$newsTable,'widgets'=>$widgets,'sessionTable'=>$sessionTable,'hideTitle'=>true,'calendar'=>$calendar,'regForm'=>$regForm]);
    }

    /**
     * @return array|mixed
     * This method is called to load articles on the site. It uses
     * an alies url parameter to identify the article.
     */
    public function pageAction()
    {
    	$alias = $this->params('alias');
    	
    	$articlesTable = new ArticlesTable($this->getServiceLocator());
    	  
    	if (empty($alias) || !$articlesTable->aliasExists($alias)) {
    		return $this->forward()->dispatch('Application\Controller\Index',array('action'=>'index'));
    	}
    	
    	$row = $articlesTable->getRecordWithAlias($alias);
    	return $this->getViewModel(array('row'=>$row,'pageTitle'=>$row->article_name,'content'=>$row->article_content));
    	
    }

    /**
     * @return ViewModel
     * This method loads the css for the site. It reads the 'style.css' file and
     * modifies it based on the user's customizations in the settings page
     *
     */
    public function cstyleAction(){

        $color = $this->params()->fromQuery('base-color');
        $replace= $this->params()->fromQuery('replace-color');
        $filePath= $this->params()->fromQuery('file-path');

        $color= explode('-',$color);

       // $color = ['2c4167'];
        $settingTable = new SettingTable($this->getServiceLocator());
        $container = new Container('style');

            $pcolor = $replace;
          //  $container->style = file_get_contents('public/themes/frontend/css/style.css');
            $style = file_get_contents($filePath);
            if(!empty($pcolor)){
                foreach($color as $value){
                    $style = str_ireplace($value,$pcolor,$style);
                }

            }



        header('Content-Type: text/css');
        $viewModel = new ViewModel(['data'=>$style]);
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    /**
     * @return array|ViewModel
     * This action is for the contact page.
     * it processes the form via a postback
     */
    public function contactAction()
    {
    	 
    	if($this->request->isPost())
    	{
    		$post= $this->request->getPost();
    		if (!empty($post['name']) && !empty($post['f_email']) && !empty($post['message']) && empty($post['email'])) {
    			 
    			 
    			$name= $post['name']; 
    			$message =$post['message'];
    			$subject = $post['subject'];
    			$f_email = $post['f_email'];
    			$mailBody = "
".__('Name').": $name<br/>
".__('Email').":$f_email
$message
    					"; 
    			$headers = __('From').": $f_email" ;
    			//mail($this->getSetting('general_admin_email',$this->getServiceLocator()), $subject, $mailBody,$headers);
    			$this->sendEmail($this->getSetting('general_admin_email'),$subject,$message,null,$name,$f_email);
                return new ViewModel(array('message'=>__('message-sent')));
    		}
    	}
    	return $this->getViewModel(array('pageTitle'=>__('Contact Us')));
    
    }

    /**
     * @return array
     * @throws \Exception
     * This displays the details of a class
     */
    public function showclassAction(){
        $id = $this->params('id');
        $lessonTable = new LessonTable($this->getServiceLocator());
        $row = $lessonTable->getRecord($id);
        return $this->getViewModel(['row'=>$row,'pageTitle'=>$row->lesson_name]);
    }

    public function calendarAction()
    {

    }

    public function migrateAction(){

        if(!defined('USER_ID')){
//            exit('unauthorized');
        }
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
        if(preg_match('#All Done#',$log)){
            $status = true;
        }
        else{
            $status=false;
        }
        $data = [
            'log'=> nl2br($log),
            'status'=>$status
        ];
        exit(json_encode($data));


    }

    public function testAction(){


        $siteUrl = $this->absoluteRoute('application/signin');
        exit(sanitizeHtml('cool'));
        dd($this->absoluteRoute('home',[]));
/*        $results = [];

     foreach(TemplateOption::get() as $row){
         $label = $row->group;
         $newLabel = strtolower($label);
         $newLabel= str_ireplace(' ','-',$newLabel);
         $results[$newLabel] = $label;

     }

        foreach($results as $key=>$value){
            echo "'{$key}'=>'{$value}', <br/>";
        }
        exit();*/

    }


    public function termsAction(){
        return $this->getViewModel(['pageTitle'=>__('terms-conditions'),'content'=>$this->getSetting('info_terms')]);
    }

    public function privacyAction(){
        return $this->getViewModel(['pageTitle'=>__('Privacy Policy'),'content'=>$this->getSetting('info_privacy')]);

    }

    public function videoAction(){
        $id= $this->params('id');
        $video= Video::find($id);
        $video->ready = 1;
        $video->save();
        exit('Video updated');

    }

    public function changecurrencyAction(){
        $id = $this->params('id');
        $session= new Container('currency');
        $session->currency_id = $id;
        getCart()->updateInvoice();
        return $this->goBack();
    }



}
