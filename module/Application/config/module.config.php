<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;



use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;
$mode= getenv('APP_MODE');
if($mode=='live'){
    $exceptions = false;
}
else{
    $exceptions = true;
}
return [
    'router' => array(
        'routes' => array(
            'page' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/[:alias]',
                    'constraints' => array(
                        //     'alias' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'page',
                    ),
                ),
            ),
            'home' => array(
                'type' => Literal::class,
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'apiv1' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/api/v1/[:resource[/:id]]',
                    'defaults' => [
                        'middleware' => 'Application\Middleware\ApiMiddleware',
                    ],
                ],
            ],
            'apiv1video' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/api/v1/[:resource[/:id[/:extension]]]',
                    'defaults' => [
                        'middleware' => 'Application\Middleware\ApiMiddleware',
                    ],
                ],
            ],
            'mobile-pay' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/mobile/pay',
                    'defaults' => [
                        'controller' => 'Application\Controller\Mobile',
                        'action' => 'load'
                    ],
                ],
            ],
            'mobile-close' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/mobile/close',
                    'defaults' => [
                        'controller' => 'Application\Controller\Mobile',
                        'action' => 'close'
                    ],
                ],
            ],
            'update-video' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/update-video[/:id]',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action' => 'video'
                    ],
                ],
            ],
            'cart' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/order/cart',
                    'defaults' => [
                        'controller' => 'Application\Controller\Cart',
                        'action' => 'index'
                    ],
                ],
            ],
            'set-session' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/cart/setsession[/:id]',
                    'defaults' => [
                        'controller' => 'Application\Controller\Cart',
                        'action' => 'setsession'
                    ],
                ],
            ],
            'remove-session' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/cart/remove[/:id]',
                    'defaults' => [
                        'controller' => 'Application\Controller\Cart',
                        'action' => 'remove'
                    ],
                ],
            ],
            'change-currency' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/change-currency[/:id]',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action' => 'changecurrency'
                    ],
                ],
            ],
            'migrate' => array(
                'type' => Literal::class,
                'options' => array(
                    'route'    => '/db/migrate',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'migrate',
                    ),
                ),
            ),
            'survey' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/survey[/:hash]',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'survey',
                        'action'        => 'survey',
                    ],
                ],
            ],
            'survey-complete' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/survey-complete',
                    'defaults' => [
                        'controller' => 'Application\Controller\Survey',
                        'action' => 'complete'
                    ],
                ],
            ],


            'style' => array(
                'type' => Literal::class,
                'options' => array(
                    'route'    => '/theme-styles.css',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'cstyle',
                    ),
                ),
            ),
            'news' => array(
                'type' => Literal::class,
                'options' => array(
                    'route'    => '/page/blog',
                    'defaults' => array(
                        'controller' => 'Application\Controller\News',
                        'action'     => 'index',
                    ),
                    'may_terminate' => true,
                ),
            ),
            'news-entry' => array(
                'type' => Segment::class,
                'options' => array(
                    'route'    => '/page/blog/post[/:id]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\News',
                        'action'     => 'view',
                    ),
                    'may_terminate' => true,
                ),
            ),
            'contact' => array(
                'type' => Literal::class,
                'options' => array(
                    'route'    => '/page/contact',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'contact',
                    ),
                    'may_terminate' => true,
                ),
            ),
            'terms' => array(
                'type' => Literal::class,
                'options' => array(
                    'route'    => '/page/terms',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'terms',
                    ),
                    'may_terminate' => true,
                ),
            ),
            'privacy' => array(
                'type' => Literal::class,
                'options' => array(
                    'route'    => '/page/privacy-policy',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'privacy',
                    ),
                    'may_terminate' => true,
                ),
            ),

            'class' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/class[/:id]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'index',
                        'action'        => 'showclass',
                    ),
                ),
            ),

            'confirm' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/confirm',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'login',
                        'action'        => 'activate',
                    ),
                ),
            ),
            'confirm_email' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/confirm-email',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'login',
                        'action'        => 'confirmmail',
                    ),
                ),
            ),
            'calendar' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/calendar',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'student',
                        'action'        => 'enroll',
                    ),
                ),
            ),
            'sessions' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/sessions',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'catalog',
                        'action'        => 'sessions',
                    ),
                ),
            ),
            'session-details' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/session-details[/:id]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'student',
                        'action'        => 'timetable',
                    ),
                ),
            ),
            'courses' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/online-courses',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'catalog',
                        'action'        => 'courses',
                    ),
                ),
            ),
            'course-details' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/course-details[/:id[/:slug]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'catalog',
                        'action'        => 'course',
                    ),
                ),
            ),
            'view-class' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/view-class[/:sessionId[/:classId]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'course',
                        'action'        => 'class',
                    ),
                ),
            ),
            'view-lecture' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/view-lecture[/:sessionId[/:lectureId]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'course',
                        'action'        => 'lecture',
                    ),
                ),
            ),
            'class-file' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/class-file[/:sessionId[/:id]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'course',
                        'action'        => 'classfile',
                    ),
                ),
            ),
            'class-files' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/class-files[/:sessionId[/:id]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'course',
                        'action'        => 'allclassfiles',
                    ),
                ),
            ),
            'lecture-file' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/lecture-file[/:sessionId[/:id]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'course',
                        'action'        => 'lecturefile',
                    ),
                ),
            ),
            'lecture-files' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/lecture-files[/:sessionId[/:id]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'course',
                        'action'        => 'alllecturefiles',
                    ),
                ),
            ),
            'cron' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/cron',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'cron',
                        'action'        => 'index',
                    ),
                ),
            ),
            'cron-classes' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/cron/classes',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'cron',
                        'action'        => 'classes',
                    ),
                ),
            ),
            'cron-homework' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/cron/homework',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'cron',
                        'action'        => 'homework',
                    ),
                ),
            ),
            'cron-courses' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/cron/courses',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'cron',
                        'action'        => 'courses',
                    ),
                ),
            ),
            'cron-started' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/cron/started',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'cron',
                        'action'        => 'started',
                    ),
                ),
            ),
            'cron-tests' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/cron/tests',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'cron',
                        'action'        => 'tests',
                    ),
                ),
            ),
            'cron-forum' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/cron/forum',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'cron',
                        'action'        => 'forum',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'shopping-cart' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/cart',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Cart',
                        'action'        => 'index',
                    ),

                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/[:action[/:id]]',
                            'constraints' => array(
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Cart',
                            ),

                        ),
                    ),
                    ),
                ),
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/student',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),

                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/[:controller[/:action[/:id]]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),

                        ),
                    ),
                    'payu-ipn' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/payu-ipn[/:id]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Callback',
                                'action'        => 'payuipn',
                            ),
                        ),
                    ),
                    'payfast-ipn' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/payfast-ipn[/:id]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Callback',
                                'action'        => 'payfastitn',
                            ),
                        ),
                    ),
                    'ipay-ipn' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/ipay-ipn[/:id]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Callback',
                                'action'        => 'ipayipn',
                            ),
                        ),
                    ),
                    'payment' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/payment',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'payment',
                                'action'        => 'index',
                            ),
                        ),
                    ),
                    'signin' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/signin',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Login',
                                'action'        => 'index',
                            ),
                        ),
                    ),
                    'process' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/process',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Login',
                                'action'        => 'process',
                            ),
                        ),
                    ),
                    'logout' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/logout',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Login',
                                'action'        => 'logout',
                            ),
                        ),
                    ),
                    'reset' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/reset',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Login',
                                'action'        => 'reset',
                            ),
                        ),
                    ),
                    'change-password' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/change-password',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Login',
                                'action'        => 'changepassword',
                            ),
                        ),
                    ),
                    'social-login' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/social-login',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Login',
                                'action'        => 'social',
                            ),
                        ),
                    ),
                    'social-update' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/social-update',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Login',
                                'action'        => 'update',
                            ),
                        ),
                    ),
                    'dashboard' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/dashboard',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Student',
                                'action'        => 'index',
                            ),
                        ),
                    ),
                    'mysessions' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/my-sessions',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Student',
                                'action'        => 'mysessions',
                            ),
                        ),
                    ),
                    'homework' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/homework[/:id]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Student',
                                'action'        => 'homework',
                            ),
                        ),
                    ),
                    'test' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/test',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Test',
                                'action'        => 'index',
                            ),
                        ),
                    ),
                    'taketest' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/taketest[/:id]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Test',
                                'action'        => 'taketest',
                            ),
                        ),
                    ),
                    'processtest' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/processtest[/:id]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Test',
                                'action'        => 'processtest',
                            ),
                        ),
                    ),
                    'starttest' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/starttest[/:id]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Test',
                                'action'        => 'starttest',
                            ),
                        ),
                    ),
                    'testresult' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/testresult[/:id]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Test',
                                'action'        => 'result',
                            ),
                        ),
                    ),
                    'downloads' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/downloads',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Download',
                                'action'        => 'index',
                            ),
                        ),
                    ),
                    'download-list' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/downloads/files[/:id]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Download',
                                'action'        => 'files',
                            ),
                        ),
                    ),
                    'download-file' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/downloads/file[/:id]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Download',
                                'action'        => 'file',
                            ),
                        ),
                    ),
                    'download-all' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/downloads/all-files[/:id]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Download',
                                'action'        => 'allfiles',
                            ),
                        ),
                    ),
                    'report' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/report[/:id]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Student',
                                'action'        => 'report',
                            ),
                        ),
                    ),
                    'password' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/password',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Student',
                                'action'        => 'password',
                            ),
                        ),
                    ),
                    'profile' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/profile',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Student',
                                'action'        => 'profile',
                            ),
                        ),
                    ),
                    'welcome' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/welcome',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Student',
                                'action'        => 'welcome',
                            ),
                        ),
                    ),
                    'classes' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/classes',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Student',
                                'action'        => 'classes',
                            ),
                        ),
                    ),
                    'notes' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/notes',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Student',
                                'action'        => 'notes',
                            ),
                        ),
                    ),
                    'register' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/register',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Login',
                                'action'        => 'register',
                            ),
                        ),
                    ),
                    'registerwidget' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/registerwidget',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Login',
                                'action'        => 'registerwidget',
                            ),
                        ),
                    ),
                    'enroll' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/enroll[/:terminal]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Student',
                                'action'        => 'enroll',
                            ),
                        ),
                    ),
                    'sessionnotes' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/sessionnotes[/:id]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Student',
                                'action'        => 'sessionnotes',
                            ),
                        ),
                    ),
                    'viewnote' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/viewnote[/:id]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Student',
                                'action'        => 'viewnote',
                            ),
                        ),
                    ),
                    'discussions' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/discussions',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Student',
                                'action'        => 'discussion',
                            ),
                        ),
                    ),
                    'viewdiscussion' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/view-discussion[/:id]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Student',
                                'action'        => 'viewdiscussion',
                            ),
                        ),
                    ),
                    'certificates' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/certificates',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Student',
                                'action'        => 'certificates',
                            ),
                        ),
                    ),
                    'certificate' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/certificate[/:id]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Student',
                                'action'        => 'certificate',
                            ),
                        ),
                    ),
                    'download-certificate' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/download-certificate[/:id]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Student',
                                'action'        => 'downloadcertificate',
                            ),
                        ),
                    ),
                    'assignments' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/assignments',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Assignment',
                                'action'        => 'index',
                            ),
                        ),
                    ),
                    'submit-assignment' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/submit-assignment[/:id]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Assignment',
                                'action'        => 'submit',
                            ),
                        ),
                    ),
                    'edit-assignment' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/edit-assignment[/:id]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Assignment',
                                'action'        => 'edit',
                            ),
                        ),
                    ),
                    'assignment-submissions' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/assignment-submissions',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Assignment',
                                'action'        => 'submissions',
                            ),
                        ),
                    ),


                ),
            ),
        ),
    ),
    'service_manager' => array(
        'aliases'=>[
           'Zend\Session\SessionManager'=>'SessionManager'
        ],
        'invokables' => array(
            'Application\View\UnauthorizedStrategy' => 'Application\View\UnauthorizedStrategy',
            'middleware' => 'Application\Middleware\ApiMiddleware'
        ),

    ),
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => \Application\Controller\ControllerFactory::class,
            Controller\NewsController::class=> \Application\Controller\ControllerFactory::class,
            Controller\LoginController::class => \Application\Controller\ControllerFactory::class,
            Controller\StudentController::class=> \Application\Controller\ControllerFactory::class,
            Controller\PaymentController::class=> \Application\Controller\ControllerFactory::class,
           Controller\MethodController::class => \Application\Controller\ControllerFactory::class,
              Controller\CallbackController::class  => \Application\Controller\ControllerFactory::class,
              Controller\TestController::class  => \Application\Controller\ControllerFactory::class,
              Controller\CronController::class  => \Application\Controller\ControllerFactory::class,
              Controller\DownloadController::class  => \Application\Controller\ControllerFactory::class,
              Controller\CatalogController::class  => \Application\Controller\ControllerFactory::class,
              Controller\AssignmentController::class  => \Application\Controller\ControllerFactory::class,
              Controller\CourseController::class  => \Application\Controller\ControllerFactory::class,
            Controller\ForumController::class  => \Application\Controller\ControllerFactory::class,
            Controller\CartController::class  => \Application\Controller\ControllerFactory::class,
            Controller\MobileController::class => \Application\Controller\ControllerFactory::class,
            Controller\SurveyController::class => \Application\Controller\ControllerFactory::class,
        ],
        'aliases' => [
            'Application\Controller\Index' => \Application\Controller\IndexController::class,
            'Application\Controller\News' => 'Application\Controller\NewsController',
            'Application\Controller\Login' => 'Application\Controller\LoginController',
            'Application\Controller\Student' => 'Application\Controller\StudentController',
            'Application\Controller\Payment' => 'Application\Controller\PaymentController',
            'Application\Controller\Method' => 'Application\Controller\MethodController',
            'Application\Controller\Callback' => 'Application\Controller\CallbackController',
            'Application\Controller\Test' => 'Application\Controller\TestController',
            'Application\Controller\Cron' => 'Application\Controller\CronController',
            'Application\Controller\Download' => 'Application\Controller\DownloadController',
            'Application\Controller\Catalog' => 'Application\Controller\CatalogController',
            'Application\Controller\Assignment' => 'Application\Controller\AssignmentController',
            'Application\Controller\Course' => 'Application\Controller\CourseController',
            'Application\Controller\Forum' => 'Application\Controller\ForumController',
            'Application\Controller\Cart' => 'Application\Controller\CartController',
            'Application\Controller\Mobile' => 'Application\Controller\MobileController',
            'Application\Controller\Survey' => 'Application\Controller\SurveyController',
        ],

    ],
    'view_manager' => [
        'display_not_found_reason' => false,
        'display_exceptions'       => false,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'strategies' => array(
        'ViewJsonStrategy',
    ),
    'module_layouts' => array(
        'Admin' => 'layout/admin',
    ),
    'view_helpers' => array(

        'factories'=>[
            View\Helper\GetSetting::class =>  \Application\View\Helper\ViewHelperFactory::class,
            View\Helper\Alert::class =>  \Application\View\Helper\ViewHelperFactory::class,
            View\Helper\GetArticle::class =>  \Application\View\Helper\ViewHelperFactory::class,
            View\Helper\GetNews::class =>  \Application\View\Helper\ViewHelperFactory::class,
            View\Helper\LoggedIn::class =>  \Application\View\Helper\ViewHelperFactory::class,
            View\Helper\GetTemplateOption::class =>  \Application\View\Helper\ViewHelperFactory::class,
            View\Helper\GetAdmin::class =>  \Application\View\Helper\ViewHelperFactory::class,
            View\Helper\AdminName::class =>  \Application\View\Helper\ViewHelperFactory::class,
            View\Helper\GetAdminInfo::class =>  \Application\View\Helper\ViewHelperFactory::class,
            View\Helper\GetStudent::class =>  \Application\View\Helper\ViewHelperFactory::class,
            View\Helper\GetMenu::class =>  \Application\View\Helper\ViewHelperFactory::class,
            View\Helper\GetCategories::class =>  \Application\View\Helper\ViewHelperFactory::class,
            View\Helper\GetClass::class =>  \Application\View\Helper\ViewHelperFactory::class,
            View\Helper\GetServiceLocator::class =>  \Application\View\Helper\ViewHelperFactory::class,
            View\Helper\FormatPrice::class =>  \Application\View\Helper\ViewHelperFactory::class,
            View\Helper\HasPermission::class =>  \Application\View\Helper\ViewHelperFactory::class,
            View\Helper\HasPermissionPath::class =>  \Application\View\Helper\ViewHelperFactory::class,
            View\Helper\HasGroupPermission::class =>  \Application\View\Helper\ViewHelperFactory::class
        ],
        'aliases'=>[
            'getSetting'=>'Application\View\Helper\GetSetting',
            'alert' => 'Application\View\Helper\Alert',
            'getArticle' => 'Application\View\Helper\GetArticle',
            'getNews' => 'Application\View\Helper\GetNews',
            'loggedIn' => 'Application\View\Helper\LoggedIn',
            'getTemplateOption'=>'Application\View\Helper\GetTemplateOption',
            'getAdmin'=>'Application\View\Helper\GetAdmin',
            'adminName'=>'Application\View\Helper\AdminName',
            'getAdminInfo'=>'Application\View\Helper\GetAdminInfo',
            'getStudent'=>'Application\View\Helper\GetStudent',
            'getMenu'=>'Application\View\Helper\GetMenu',
            'getCategories'=>'Application\View\Helper\GetCategories',
            'getClass'=>'Application\View\Helper\GetClass',
            'getServiceLocator'=>'Application\View\Helper\GetServiceLocator',
            'formatPrice'=>'Application\View\Helper\FormatPrice',
            'hasPermission'=>'Application\View\Helper\HasPermission',
            'hasPermissionPath'=>'Application\View\Helper\HasPermissionPath',
            'hasGroupPermission'=>'Application\View\Helper\HasGroupPermission'
        ],
        /*
        'invokables' => array(
            'Alert' => 'Application\View\Helper\Alert',
            'GetArticle' => 'Application\View\Helper\GetArticle',
            'GetNews' => 'Application\View\Helper\GetNews',
            'LoggedIn' => 'Application\View\Helper\LoggedIn',
            'GetSetting'=>'Application\View\Helper\GetSetting',
            'GetTemplateOption'=>'Application\View\Helper\GetTemplateOption',
            'GetAdmin'=>'Application\View\Helper\GetAdmin',
            'AdminName'=>'Application\View\Helper\AdminName',
            'GetAdminInfo'=>'Application\View\Helper\GetAdminInfo',
            'GetStudent'=>'Application\View\Helper\GetStudent',
            'GetMenu'=>'Application\View\Helper\GetMenu',
            'GetCategories'=>'Application\View\Helper\GetCategories',
            'GetClass'=>'Application\View\Helper\GetClass',
            'FormatPrice'=>'Application\View\Helper\FormatPrice',
            'HasPermission'=>'Application\View\Helper\HasPermission',
            'HasPermissionPath'=>'Application\View\Helper\HasPermissionPath',
            'HasGroupPermission'=>'Application\View\Helper\HasGroupPermission'
        ),
        */
    )


];
