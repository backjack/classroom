<?php


return array(
		'bjyauthorize' => array(
               'unauthorized_strategy' => 'Application\View\UnauthorizedStrategy',

				// set the 'guest' role as default (must be defined in a role provider)
				'default_role' => 'guest',

				/* this module uses a meta-role that inherits from any roles that should
				 * be applied to the active user. the identity provider tells us which
* roles the "identity role" should inherit from.
*
* for ZfcUser, this will be your default identity provider
*/
				//'identity_provider' => 'BjyAuthorize\Provider\Identity\ZfcUserZendDb',
				'identity_provider' => 'Application\Provider\Identity\UserRolesProvider',
				/* If you only have a default role and an authenticated role, you can
				 * use the 'AuthenticationIdentityProvider' to allow/restrict access
* with the guards based on the state 'logged in' and 'not logged in'.
*
* 'default_role'       => 'guest',         // not authenticated
* 'authenticated_role' => 'user',          // authenticated
* 'identity_provider'  => 'BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider',
*/
				

				/* role providers simply provide a list of roles that should be inserted
				 * into the Zend\Acl instance. the module comes with two providers, one
* to specify roles in a config file and one to load roles using a
* Zend\Db adapter.
*/
				'role_providers' => array(

						/* here, 'guest' and 'user are defined as top-level roles, with
						 * 'admin' inheriting from user
*/
						'BjyAuthorize\Provider\Role\Config' => array(
								'guest' => array(),
								'user'  => array('children' => array(
										'admin' => array(),
										'student'=>array(), 
								)),
						),

						// this will load roles from the user_role table in a database
						// format: user_role(role_id(varchar), parent(varchar))
						/*
						'BjyAuthorize\Provider\Role\ZendDb' => array(
								'table'                 => 'user_role',
								'identifier_field_name' => 'id',
								'role_id_field'         => 'role_id',
								'parent_role_field'     => 'parent_id',
						),
						*/
						// this will load roles from
						// the 'BjyAuthorize\Provider\Role\ObjectRepositoryProvider' service
						/*
						'BjyAuthorize\Provider\Role\ObjectRepositoryProvider' => array(
								// class name of the entity representing the role
								'role_entity_class' => 'My\Role\Entity',
								// service name of the object manager
								'object_manager'    => 'My\Doctrine\Common\Persistence\ObjectManager',
						),
						*/
				),

				// resource providers provide a list of resources that will be tracked
				// in the ACL. like roles, they can be hierarchical
				'resource_providers' => array(
						'BjyAuthorize\Provider\Resource\Config' => array(
								'pants' => array(),
						),
				),

				/* rules can be specified here with the format:
				 * array(roles (array), resource, [privilege (array|string), assertion])
* assertions will be loaded using the service manager and must implement
* Zend\Acl\Assertion\AssertionInterface.
* *if you use assertions, define them using the service manager!*
*/
				'rule_providers' => array(
						'BjyAuthorize\Provider\Rule\Config' => array(
								'allow' => array(
										// allow guests and users (and admins, through inheritance)
										// the "wear" privilege on the resource "pants"
								//		array(array('guest', 'user'), 'pants', 'wear')
								),

								// Don't mix allow/deny rules if you are using role inheritance.
								// There are some weird bugs.
								'deny' => array(
										// ...
								),
						),
				),

				/* Currently, only controller and route guards exist
				 *
* Consider enabling either the controller or the route guard depending on your needs.
*/
				'guards' => array(
						/* If this guard is specified here (i.e. it is enabled), it will block
						 * access to all controllers and actions unless they are specified here.
* You may omit the 'action' index to allow access to the entire controller
*/
						/*
						'BjyAuthorize\Guard\Controller' => array(
								array('controller' => 'index', 'action' => 'index', 'roles' => array('guest','user')),
								array('controller' => 'index', 'action' => 'stuff', 'roles' => array('user')),
								// You can also specify an array of actions or an array of controllers (or both)
								// allow "guest" and "admin" to access actions "list" and "manage" on these "index",
								// "static" and "console" controllers
								array(
										'controller' => array('index', 'static', 'console'),
										'action' => array('list', 'manage'),
										'roles' => array('guest', 'admin')
								),
								array(
										'controller' => array('search', 'administration'),
										'roles' => array( 'admin')
								),
								array('controller' => 'login', 'roles' => array()),
								// Below is the default index action used by the ZendSkeletonApplication
								// array('controller' => 'Application\Controller\Index', 'roles' => array('guest', 'user')),
						),
						*/
						/* If this guard is specified here (i.e. it is enabled), it will block
						 * access to all routes unless they are specified here.
*/
						'BjyAuthorize\Guard\Route' => array(
                            array('route' => 'survey', 'roles' => array('guest','user')),
                            array('route' => 'survey-complete', 'roles' => array('guest','user')),
                            array('route' => 'mobile-pay', 'roles' => array('guest','user')),
                            array('route' => 'mobile-close', 'roles' => array('guest','user')),
                            array('route' => 'features', 'roles' => array('guest','user')),
								array('route' => 'zfcuser/login', 'roles' => array('guest')),
								array('route' => 'zfcuser/register', 'roles' => array('guest')),
								array('route' => 'nosite', 'roles' => array('guest','admin')),
								array('route' => 'forgot', 'roles' => array('guest','admin')),
								array('route' => 'reset', 'roles' => array('guest','admin')),
								// Below is the default index action used by the ZendSkeletonApplication
								array('route' => 'home', 'roles' => array('guest', 'user')),
                                array('route' => 'apiv1', 'roles' => array('guest', 'user')),
                                array('route' => 'apiv1video', 'roles' => array('guest', 'user')),

                                array('route' => 'migrate', 'roles' => array('guest', 'user')),
                                array('route' => 'update-video', 'roles' => array('guest', 'user')),
                                array('route' => 'cart', 'roles' => array('guest', 'user')),
                                array('route' => 'set-session', 'roles' => array('guest', 'user')),
                            array('route' => 'remove-session', 'roles' => array('guest', 'user')),
                                array('route' => 'change-currency', 'roles' => array('guest', 'user')),
								array('route' => 'page', 'roles' => array('guest', 'user')),
								array('route' => 'news', 'roles' => array('guest', 'user')),
                                array('route' => 'news-entry', 'roles' => array('guest', 'user')),
                                array('route' => 'style', 'roles' => array('guest', 'user')),
								array('route' => 'contact', 'roles' => array('guest', 'user')),
                                array('route' => 'class', 'roles' => array('guest', 'user')),
                                array('route' => 'cron', 'roles' => array('guest', 'user')),
                                array('route' => 'terms', 'roles' => array('guest', 'user')),
                                array('route' => 'privacy', 'roles' => array('guest', 'user')),
                                array('route' => 'cron-classes', 'roles' => array('guest', 'user')),
                                array('route' => 'cron-homework', 'roles' => array('guest', 'user')),
                                array('route' => 'cron-courses', 'roles' => array('guest', 'user')),
                                array('route' => 'cron-started', 'roles' => array('guest', 'user')),
                                array('route' => 'cron-tests', 'roles' => array('guest', 'user')),
                                array('route' => 'cron-forum', 'roles' => array('guest', 'user')),
                                array('route' => 'calendar', 'roles' => array('guest', 'user')),
                                array('route' => 'sessions', 'roles' => array('guest', 'user')),
                                array('route' => 'confirm_email', 'roles' => array('guest', 'user')),
                            array('route' => 'confirm', 'roles' => array('guest', 'user')),
                                array('route' => 'session-details', 'roles' => array('guest', 'user')),
                            array('route' => 'courses', 'roles' => array('guest', 'user')),
                            array('route' => 'course-details', 'roles' => array('guest', 'user')),
                            array('route' => 'SanCaptcha/captcha_form_generate', 'roles' => array('guest', 'user')),
                            array('route' => 'shopping-cart/default', 'roles' => array('guest', 'user')),
								array('route' => 'application/default', 'roles' => array('student')),
								array('route' => 'application/signin', 'roles' => array('guest', 'user')),
                            array('route' => 'application/reset', 'roles' => array('guest', 'user')),
								array('route' => 'application/process', 'roles' => array('guest', 'user')),
								array('route' => 'application/logout', 'roles' => array('guest', 'user')),
                                array('route' => 'application/register', 'roles' => array('guest', 'user')),
                            array('route' => 'application/registerwidget', 'roles' => array('guest', 'user')),
                        array('route' => 'application/social-login', 'roles' => array('guest', 'user')),
                            array('route' => 'application/payu-ipn', 'roles' => array('guest', 'user')),
                            array('route' => 'application/payfast-ipn', 'roles' => array('guest', 'user')),
                            array('route' => 'application/ipay-ipn', 'roles' => array('guest', 'user')),
                            array('route' => 'application/social-update', 'roles' => array('student')),
                            array('route' => 'view-class', 'roles' => array('student')),
                            array('route' => 'view-lecture', 'roles' => array('student')),
                                array('route' => 'application/payment', 'roles' => array('student')),
								array('route' => 'application/children', 'roles' => array('student')),
								array('route' => 'application/report', 'roles' => array('student')),
								array('route' => 'application/homework', 'roles' => array('student')),
								array('route' => 'application/password', 'roles' => array('student')),
                                array('route' => 'application/profile', 'roles' => array('student')),
                                array('route' => 'application/classes', 'roles' => array('student')),
                                 array('route' => 'application/welcome', 'roles' => array('student')),
                                array('route' => 'application/notes', 'roles' => array('student')),
                                array('route' => 'application/test', 'roles' => array('student')),
                                array('route' => 'application/taketest', 'roles' => array('student')),
                            array('route' => 'application/processtest', 'roles' => array('student')),
                            array('route' => 'application/starttest', 'roles' => array('student')),
                                array('route' => 'application/testresult', 'roles' => array('student')),
                            array('route' => 'application/dashboard', 'roles' => array('student')),
                            array('route' => 'application/mysessions', 'roles' => array('student')),
                            array('route' => 'class-file', 'roles' => array('student')),
                            array('route' => 'class-files', 'roles' => array('student')),
                            array('route' => 'lecture-file', 'roles' => array('student')),
                            array('route' => 'lecture-files', 'roles' => array('student')),

                            array('route' => 'application/downloads', 'roles' => array('student')),
                            array('route' => 'application/download-list', 'roles' => array('student')),
                            array('route' => 'application/download-file', 'roles' => array('student')),
                            array('route' => 'application/download-all', 'roles' => array('student')),


                            array('route' => 'application/assignments', 'roles' => array('student')),
                            array('route' => 'application/submit-assignment', 'roles' => array('student')),
                            array('route' => 'application/edit-assignment', 'roles' => array('student')),
                            array('route' => 'application/assignment-submissions', 'roles' => array('student')),



                                array('route' => 'application/enroll', 'roles' => array('guest', 'user')),
                            array('route' => 'application/change-password', 'roles' => array('guest', 'user')),
                                array('route' => 'application/sessionnotes', 'roles' => array('student')),
                                array('route' => 'application/viewnote', 'roles' => array('student')),
                                array('route' => 'application/discussions', 'roles' => array('student')),
                            array('route' => 'application/viewdiscussion', 'roles' => array('student')),
                            array('route' => 'application/certificates', 'roles' => array('student')),
                         //   array('route' => 'application/certificate', 'roles' => array('student')),
                            array('route' => 'application/download-certificate', 'roles' => array('student')),
						        array('route' => 'admin/default', 'roles' => array('admin')),
                                array('route' => 'admin/payments', 'roles' => array('admin')),
						        array('route' => 'admin/signin', 'roles' => array('guest', 'user','student','admin')),
						        array('route' => 'admin/process', 'roles' => array('guest', 'user','student','admin')),
						    	array('route' => 'admin/logout', 'roles' => array('guest', 'user')),
                                array('route' => 'admin/reset', 'roles' => array('guest', 'user')),
                            array('route' => 'admin/change-password', 'roles' => array('guest', 'user')),


                            array('route' => 'view-class-demo', 'roles' => array('admin')),
                            array('route' => 'view-lecture-demo', 'roles' => array('admin')),
                            array('route' => 'class-file-demo', 'roles' => array('admin')),
                            array('route' => 'class-files-demo', 'roles' => array('admin')),
                            array('route' => 'lecture-file-demo', 'roles' => array('admin')),
                            array('route' => 'lecture-files-demo', 'roles' => array('admin')),

						),
				),
		),
);