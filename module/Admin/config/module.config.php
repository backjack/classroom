<?php
namespace Admin;

use Zend\Router\Http\Literal;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Router\Http\Segment;
return [
    'router' => array(
        'routes' => array(
            'admin' => array(
                'type'    => Literal::class,
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/admin',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Admin\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    // This route is a sane default when developing a module;
                    // as you solidify the routes for your module, however,
                    // you may want to remove it and replace it with more
                    // specific routes.
                    'default' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/[:controller[/:action[/:id[/:param1[/:param2]]]]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                    'payments' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/admin/payments[/:start[/:end]]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Admin\Controller',
                                'controller'    => 'Student',
                                'action'        => 'payments',
                            ),
                        ),
                    ),
                    'signin' => array(
                        'type'    => Literal::class,
                        'options' => array(
                            'route'    => '/signin',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Admin\Controller',
                                'controller'    => 'Login',
                                'action'        => 'index',
                            ),
                        ),
                    ),
                    'reset' => array(
                        'type'    => Literal::class,
                        'options' => array(
                            'route'    => '/reset',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Admin\Controller',
                                'controller'    => 'Login',
                                'action'        => 'reset',
                            ),
                        ),
                    ),
                    'change-password' => array(
                        'type'    => Literal::class,
                        'options' => array(
                            'route'    => '/change-password',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Admin\Controller',
                                'controller'    => 'Login',
                                'action'        => 'changepassword',
                            ),
                        ),
                    ),
                    'process' => array(
                        'type'    => Literal::class,
                        'options' => array(
                            'route'    => '/process',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Admin\Controller',
                                'controller'    => 'Login',
                                'action'        => 'process',
                            ),
                        ),
                    ),
                    'logout' => array(
                        'type'    => Literal::class,
                        'options' => array(
                            'route'    => '/logout',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Admin\Controller',
                                'controller'    => 'Login',
                                'action'        => 'logout',
                            ),
                        ),
                    ),
                ),
            ),
            'view-class-demo' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/course-demo/view-class[/:sessionId[/:classId]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Admin\Controller',
                        'controller'    => 'course',
                        'action'        => 'class',
                    ),
                ),
            ),
            'view-lecture-demo' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/course-demo/view-lecture[/:sessionId[/:lectureId]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Admin\Controller',
                        'controller'    => 'course',
                        'action'        => 'lecture',
                    ),
                ),
            ),
            'class-file-demo' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/course-demo/class-file[/:sessionId[/:id]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Admin\Controller',
                        'controller'    => 'course',
                        'action'        => 'classfile',
                    ),
                ),
            ),
            'class-files-demo' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/course-demo/class-files[/:sessionId[/:id]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Admin\Controller',
                        'controller'    => 'course',
                        'action'        => 'allclassfiles',
                    ),
                ),
            ),
            'lecture-file-demo' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/course-demo/lecture-file[/:sessionId[/:id]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Admin\Controller',
                        'controller'    => 'course',
                        'action'        => 'lecturefile',
                    ),
                ),
            ),
            'lecture-files-demo' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/course-demo/lecture-files[/:sessionId[/:id]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Admin\Controller',
                        'controller'    => 'course',
                        'action'        => 'alllecturefiles',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => \Application\Controller\ControllerFactory::class,
            Controller\LoginController::class => \Application\Controller\ControllerFactory::class,
            Controller\StudentController::class => \Application\Controller\ControllerFactory::class,
            Controller\ArticlesController::class => \Application\Controller\ControllerFactory::class,
            Controller\HomeworkController::class => \Application\Controller\ControllerFactory::class,
            Controller\FilemanagerController::class => \Application\Controller\ControllerFactory::class,
            Controller\NewsController::class => \Application\Controller\ControllerFactory::class,
            Controller\AccountController::class => \Application\Controller\ControllerFactory::class,
            Controller\LessonController::class => \Application\Controller\ControllerFactory::class,
            Controller\SettingController::class => \Application\Controller\ControllerFactory::class,
            Controller\PaymentController::class => \Application\Controller\ControllerFactory::class,
            Controller\WidgetController::class => \Application\Controller\ControllerFactory::class,
            Controller\TestController::class => \Application\Controller\ControllerFactory::class,
            Controller\DiscussController::class => \Application\Controller\ControllerFactory::class,
            Controller\CertificateController::class => \Application\Controller\ControllerFactory::class,
            Controller\DownloadController::class => \Application\Controller\ControllerFactory::class,
            Controller\LectureController::class => \Application\Controller\ControllerFactory::class,
            Controller\SessionController::class => \Application\Controller\ControllerFactory::class,
            Controller\AssignmentController::class => \Application\Controller\ControllerFactory::class,
            Controller\TemplateController::class => \Application\Controller\ControllerFactory::class,
            Controller\SmsgatewayController::class => \Application\Controller\ControllerFactory::class,
            Controller\ForumController::class => \Application\Controller\ControllerFactory::class,
            Controller\ReportController::class => \Application\Controller\ControllerFactory::class,
            Controller\MessagesController::class => \Application\Controller\ControllerFactory::class,
            Controller\VideoController::class => \Application\Controller\ControllerFactory::class,
            Controller\CourseController::class=> \Application\Controller\ControllerFactory::class,
            Controller\SurveyController::class=> \Application\Controller\ControllerFactory::class,
        ],
        'aliases' => [
            'Admin\Controller\Index' => 'Admin\Controller\IndexController',
            'Admin\Controller\Login' => 'Admin\Controller\LoginController',
            'Admin\Controller\Student' => 'Admin\Controller\StudentController',
            'Admin\Controller\Articles' => 'Admin\Controller\ArticlesController',
            'Admin\Controller\Homework' => 'Admin\Controller\HomeworkController',
            'Admin\Controller\Filemanager' => 'Admin\Controller\FilemanagerController',
            'Admin\Controller\News' => 'Admin\Controller\NewsController',
            'Admin\Controller\Account' => 'Admin\Controller\AccountController',
            'Admin\Controller\Lesson' => 'Admin\Controller\LessonController',
            'Admin\Controller\Setting' => 'Admin\Controller\SettingController',
            'Admin\Controller\Payment' => 'Admin\Controller\PaymentController',
            'Admin\Controller\Widget' => 'Admin\Controller\WidgetController',
            'Admin\Controller\Test' => 'Admin\Controller\TestController',
            'Admin\Controller\Discuss' => 'Admin\Controller\DiscussController',
            'Admin\Controller\Certificate' => 'Admin\Controller\CertificateController',
            'Admin\Controller\Download' => 'Admin\Controller\DownloadController',
            'Admin\Controller\Lecture' => 'Admin\Controller\LectureController',
            'Admin\Controller\Session' => 'Admin\Controller\SessionController',
            'Admin\Controller\Assignment' => 'Admin\Controller\AssignmentController',
            'Admin\Controller\Template' => 'Admin\Controller\TemplateController',
            'Admin\Controller\Smsgateway' => 'Admin\Controller\SmsgatewayController',
            'Admin\Controller\Forum' => 'Admin\Controller\ForumController',
            'Admin\Controller\Report' => 'Admin\Controller\ReportController',
            'Admin\Controller\Messages' => 'Admin\Controller\MessagesController',
            'Admin\Controller\Video' => 'Admin\Controller\VideoController',
            'Admin\Controller\Course' => 'Admin\Controller\CourseController',
            'Admin\Controller\Survey' => 'Admin\Controller\SurveyController',
            ]
    ],
    'view_manager' => [
        'template_path_stack' => array(
            'Admin' => __DIR__ . '/../view',
        ),
    ],
    'strategies' => array(
        'ViewJsonStrategy',
    ),
];