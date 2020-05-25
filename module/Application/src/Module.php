<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Entity\Country;
use Application\Entity\Currency;
use Application\Entity\Student;
use Application\Model\PermissionTable;
use Application\Model\SettingTable;
use Application\Model\TemplateTable;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container as LaravelContainer;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use Symfony\Component\Config\Definition\Exception\Exception;
use Zend\Cache\StorageFactory;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Session\SaveHandler\Cache;
use Zend\Session\SessionManager;
use Zend\Session\Container;

use Zend\Db\TableGateway\TableGateway;
use Zend\Session\SaveHandler\DbTableGateway;
use Zend\Session\SaveHandler\DbTableGatewayOptions;
use Application\Provider\Identity\UserRolesProvider;
use Intermatics\UtilityFunctions;
use Intermatics\Mailer;
use Zend\Config\Reader\Xml;

use Zend\Session\Config\SessionConfig;

use Zend\Session\Validator;
$path='vendor/Intermatics/library/Intermatics/Opencart/Helpers/';
include_once $path.'general.php';
class Module
{
    const VERSION = '3.0.3-dev';

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        //set service manager globally
        $GLOBALS['serviceManager'] = $e->getApplication()->getServiceManager();

        $this->bootstrapSession($e);

        $this->bootstrapLayouts($e);
        $this->bootstrapEloquent($e);
        $this->setStrictMode($e);
        try{
            $this->bootstrapCurrency();
        }
        catch(\Exception $ex){

        }


        // The following line instantiates the SessionManager and automatically
        // makes the SessionManager the 'default' one.
       // $sessionManager = $serviceManager->get(SessionManager::class);

        $user= '';
        if(defined('USER_ID')){
            $user = '/'.USER_ID;
        }

        if(!defined('USER_PATH')){
            $filePath = 'public/usermedia'.$user;
            define('USER_PATH',$filePath);
        }


    }


    public function bootstrapSession($e)
    {
        $sm = $e->getApplication()
            ->getServiceManager();



         $session = $sm->get(SessionManager::class);



        try {
            $session->start();

        } catch (\Exception $e) {

            session_destroy();
            return;
        }




        $container = new Container('initialized');

        if (isset($container->init)) {
            return;
        }

        $serviceManager = $e->getApplication()->getServiceManager();
        $request        = $serviceManager->get('Request');

        $session->regenerateId(true);
        $container->init          = 1;
        $container->remoteAddr    = $request->getServer()->get('REMOTE_ADDR');
        $container->httpUserAgent = $request->getServer()->get('HTTP_USER_AGENT');

        $config = $serviceManager->get('config');
        if (! isset($config['session_manager'])) {
            return;
        }

        $sessionConfig = $config['session_manager'];

        /*
        if (! isset($sessionConfig['validators'])) {
            return;
        }

        $chain   = $session->getValidatorChain();

        foreach ($sessionConfig['validators'] as $validator) {
            switch ($validator) {
                case Validator\HttpUserAgent::class:
                    $validator = new $validator($container->httpUserAgent);
                    break;
                case Validator\RemoteAddr::class:
                    $validator  = new $validator($container->remoteAddr);
                    break;
                default:
                    $validator = new $validator();
            }

            $chain->attach('session.validate', array($validator, 'isValid'));
        }




        */



    }

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function bootstrapLayouts($e)
    {

        //check for ssl
        $settingTable = new SettingTable($e->getApplication()->getServiceManager());
        $ssl = $settingTable->getSetting('general_ssl');
        if(!empty($ssl))
        {
         /*   //check if user is not logged in
            $authService = $e->getApplication()->getServiceManager()->get('StudentAuthService');
            if(!$authService->hasIdentity())
            {

            }*/

            forceSSL();

        }

        //set timezone
        $timezone = $settingTable->getSetting('general_timezone');
        if(!empty($timezone)){
            date_default_timezone_set($timezone);
        }

        $e->getApplication()->getEventManager()->getSharedManager()->attach('Zend\Mvc\Controller\AbstractController', 'dispatch', function($e) {
            //exit('event registered');
            $controller      = $e->getTarget();
            $controllerClass = get_class($controller);
            $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
            $config          = $e->getApplication()->getServiceManager()->get('config');


            if($moduleNamespace=='Admin'){

                $action = $e->getRouteMatch()->getParam('action');
                $controllerName = $e->getRouteMatch()->getParam('controller');
                $controllerName = strtolower($controllerName);

                $route = $controllerName.'/'.$action;
                $route = str_replace('admin\controller\\', '', $route);
                $permissionTable = new PermissionTable($e->getApplication()->getServiceManager());

                if(!$permissionTable->hasPermission(trim($route))){
                    return $controller->redirect()->toRoute('admin/default',['controller'=>'index','action'=>'nopermission']);

                }

                //get admin id and set in constant
                $authService = $e->getApplication()->getServiceManager()->get('AdminAuthService');
                $identity = $authService->getIdentity();
                $email = $identity['email'];
                $accountsTable= new \Application\Model\AccountsTable($e->getApplication()->getServiceManager());
                $row = $accountsTable->getAccountWithEmail($email);
                if($row){
                    if(!defined('ADMIN_ID'))define('ADMIN_ID',$row->account_id);
                    if(!defined('GLOBAL_ACCESS')) define('GLOBAL_ACCESS',$permissionTable->hasPermission('misc/global_access'));

                }

                if (isset($config['module_layouts'][$moduleNamespace])) {
                    $controller->layout($config['module_layouts'][$moduleNamespace]);
                }

            }
            else{

                try{
                    //check if student is logged in
                    $authService = $e->getApplication()->getServiceManager()->get('StudentAuthService');
                    if($authService->hasIdentity())
                    {
                        $identity = $authService->getIdentity();
                        $email = $identity['email'];
                        //update the student last seen
                        $student = Student::where('email',$email)->first();
                        if($student){
                            $student->last_seen = time();
                            $student->save();
                        }

                    }

                }
                catch(\Exception $ex){

                }




                if(!defined('GLOBAL_ACCESS')){
                    define('GLOBAL_ACCESS',true);
                }
                //get the active template
                try{
                    $templateTable = new TemplateTable($e->getApplication()->getServiceManager());
                    $template = $templateTable->getActiveTemplate();
                    $controller->layout('layout/templates/'.$template->template_id.'/layout');
                    if(!defined('TID')){
                        define('TID',$template->template_id);
                        define('TEMPLATE','layout/templates/'.$template->template_id.'/layout');
                    }

                }
                catch(\Exception $ex){

                }

            }


        }, 200);
    }

    public function bootstrapEloquent($e){

        $sm =$e->getApplication()->getServiceManager();
        $config = $sm->get('config');
        $dbConfig = [
            'driver'    => 'mysql',
            'host'      => $config['db']['hostname'],
            'database'  => $config['db']['dbname'],
            'username'  => $config['db']['username'],
            'password'  => $config['db']['password'],
            'charset'   => 'utf8',
            'collation' => 'utf8_general_ci',
        ];

        $capsule = new Manager();
        $capsule->addConnection($dbConfig);
        $capsule->setEventDispatcher(new Dispatcher(new LaravelContainer));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();


        // Set up a current path resolver so the paginator can generate proper links
        Paginator::currentPathResolver(function () {
            return isset($_SERVER['REQUEST_URI']) ? strtok($_SERVER['REQUEST_URI'], '?') : '/';
        });
        // Set up a current page resolver
        Paginator::currentPageResolver(function ($pageName = 'page') {
            $page = isset($_REQUEST[$pageName]) ? $_REQUEST[$pageName] : 1;
            return $page;
        });



        $viewFactory = $sm->get('BladeFactory');
        Paginator::viewFactoryResolver(function() use($viewFactory){

            return $viewFactory;
        });
        Paginator::defaultView('pagination.pager');



    }

    public function setStrictMode($e){

        $table= new SettingTable();
        $table->getGateway()->getAdapter()->driver->getConnection()->execute("SET SESSION sql_mode='NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'");


    }

    public function bootstrapCurrency(){

        $settingTable = new \Application\Model\SettingTable();
        $countryId = $settingTable->getSetting('country_id');
        $country = \Application\Entity\Country::find($countryId);
        if($country){

            //check for installed currency
            $defaultCurrency = Currency::where('country_id',$country->country_id)->first();
            if(!$defaultCurrency){
                $defaultCurrency = Currency::create(['currency_id'=>$country->country_id,'exchange_rate'=>1]);
            }
        }
        else{
            return false;
        }

        $session = new Container('currency');
        if(!isset($session->currency_id)){
            //get visitor location
            $country = getCountry();

            //get country entry
            $countryModel = Country::where('iso_code_2',strtoupper($country))->first();

            if($countryModel){
                //check if this country is installed as a currency
                $currency = Currency::where('country_id',$countryModel->country_id)->first();
                if($currency){
                    $session->currency_id = $currency->currency_id;
                }
                else{
                    $session->currency_id = $defaultCurrency->currency_id;

                }

            }
            else{
                $session->currency_id = $defaultCurrency->currency_id;
            }

        }


    }

    public function getServiceConfig()
    {
        return array(
            'services'=>[

            ],
            'factories'=>array(
                'SessionManager' => function ($container) {

                    $config = $container->get('config');
                    if (! isset($config['session'])) {

                        $sessionManager = new SessionManager();
                        Container::setDefaultManager($sessionManager);
                        return $sessionManager;
                    }


                    $session = $config['session'];

                    $sessionConfig = null;
                    if (isset($session['config'])) {
                        $class = isset($session['config']['class'])
                            ?  $session['config']['class']
                            : SessionConfig::class;

                        $options = isset($session['config']['options'])
                            ?  $session['config']['options']
                            : [];

                        $sessionConfig = new $class();
                        $sessionConfig->setOptions($options);
                    }

                    $sessionStorage = null;
                    if (isset($session['storage'])) {
                        $class = $session['storage'];
                        $sessionStorage = new $class();
                    }

                    $sessionSaveHandler = null;
                    if (isset($session['save_handler'])) {
                        // class should be fetched from service manager
                        // since it will require constructor arguments
                        $sessionSaveHandler = $container->get($session['save_handler']);
                    }

                    $sessionManager = new SessionManager(
                        $sessionConfig,
                        $sessionStorage,
                        $sessionSaveHandler
                    );

                    Container::setDefaultManager($sessionManager);
                    return $sessionManager;
                },
                'Mailer'=>function($sm){
                    $mailer = new Mailer($sm);
                    return $mailer;
                },
                'CustomConfig'=>function($sm){

                    $reader = new Xml();
                    $data   = $reader->fromFile('config/custom/global.xml');
                    return $data;
                },
                'StudentAuthService'=>function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'student','email','password');

                    $authService = new AuthenticationService();
                    $authService->setAdapter($dbTableAuthAdapter);
                    return $authService;
                },
                'AdminAuthService'=>function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'accounts','email','password');

                    $authService = new AuthenticationService();
                    $authService->setAdapter($dbTableAuthAdapter);
                    return $authService;
                },
                'Application\Provider\Identity\UserRolesProvider' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                    //get role
                    $role = UtilityFunctions::getRole();
                    $service = null;
                    switch ($role)
                    {

                        case 'student':
                            $service = $sm->get('StudentAuthService');
                            break;
                        case 'admin':
                            $service = $sm->get('AdminAuthService');
                            break;
                        default:
                            $service = $sm->get('AdminAuthService');
                            break;

                    }

                    $userRolesProvider=new UserRolesProvider($service);
                    $userRolesProvider->setDbAdapter($dbAdapter);
                    return $userRolesProvider;
                },
                'DBSaveHandler'=> function($sm){

                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');

                    $resultSet = $adapter->query("SHOW TABLES LIKE 'session_storage'", \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
                    $total  = $resultSet->count();

                    if($total > 0){

                        $tableGateway = new TableGateway('session_storage', $adapter);

                        $saveHandler  = new DbTableGateway($tableGateway, new DbTableGatewayOptions());

                      return $saveHandler;
                    }
                    else{
                        return null;
                    }
                },
                'BladeFactory'=>function($sm){
                    $pathsToTemplates = ['module/Application/blade/views','module/Application/view','module/Admin/view'];

                    $pathToCompiledTemplates = 'module/Application/blade/compiled';

                    //make sure directory exists
                    if(!is_dir($pathToCompiledTemplates)){
                        @mkdir($pathToCompiledTemplates);
                    }

                    $filesystem = new Filesystem;
                    $eventDispatcher = new Dispatcher(new LaravelContainer);
                    // Create View Factory capable of rendering PHP and Blade templates
                    $viewResolver = new EngineResolver;
                    $bladeCompiler = new BladeCompiler($filesystem, $pathToCompiledTemplates);
                    $viewResolver->register('blade', function () use ($bladeCompiler, $filesystem) {
                        return new CompilerEngine($bladeCompiler, $filesystem);
                    });
                    $viewResolver->register('php', function () {
                        return new PhpEngine;
                    });
                    $viewFinder = new FileViewFinder($filesystem, $pathsToTemplates);
                    $viewFactory = new Factory($viewResolver, $viewFinder, $eventDispatcher);
                    return $viewFactory;
                }


            ),
        );
    }


}
