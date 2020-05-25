<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
use Zend\Session\Storage\SessionArrayStorage;
use Zend\Session\Validator\RemoteAddr;
use Zend\Session\Validator\HttpUserAgent;
use Zend\Session;
return [
    'db' => [
        'driver' => 'Pdo',
        'driver_options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ],
    ],
    'site'=>array(
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Home',
                'route' => 'home',
                'pages' => array(
                    array(
                        'label'=>'Dashboard',
                        'route'=>'application/dashboard',
                        'pages'=>[
                            [
                                'label'=>'My Account',
                                'route'=>'application/profile',
                            ],
                            [
                                'label'=>'Classes Attended',
                                'route'=>'application/classes',
                            ],
                            [
                                'label'=>'My Sessions',
                                'route'=>'application/mysessions',
                            ],
                            [
                                'label'=>'My Invoices',
                                'route'=>'application/default',
                                'controller'=>'student',
                                'action'=>'invoices'
                            ],
                            [
                                'label'=>'Surveys',
                                'route'=>'application/default',
                                'controller'=>'student',
                                'action'=>'surveys',
                                'pages'=>[
                                    [
                                        'label'=>'Survey',
                                        'route'=>'survey',
                                    ],
                                    [
                                        'label'=>'Survey Submitted',
                                        'route'=>'survey-complete',
                                    ],

                                ]
                            ],
                            [
                                'label'=>'My Bookmarks',
                                'route'=>'application/default',
                                'controller'=>'course',
                                'action'=>'bookmarks'
                            ],
                            [
                                'label'=>'Your Cart',
                                'route'=>'cart',
                                'pages'=>[
                                    [
                                        'label'=>'Payment Method',
                                        'route'=>'application/default',
                                        'module'=>'application',
                                        'controller'=>'payment',
                                        'action'=>'method',
                                    ]
                                ]
                            ],
                            [
                                'label'=>'Revision Notes',
                                'route'=>'application/notes',
                                'pages'=>[
                                    [
                                        'label'=>'Notes',
                                        'route'=>'application/sessionnotes'
                                    ],
                                    [
                                        'label'=>'View Note',
                                        'route'=>'application/viewnote'
                                    ]
                                ]
                            ],
                            [
                                'label'=>'Instructor Chat',
                                'route'=>'application/discussions',
                                'pages'=>[
                                    [
                                        'label'=>'View Chat',
                                        'route'=>'application/viewdiscussion'
                                    ],

                                ]
                            ],
                            [
                                'label'=>'Tests',
                                'route'=>'application/test',
                                'pages'=>[
                                    [
                                        'label'=>'Take Test',
                                        'route'=>'application/taketest'
                                    ],
                                    [
                                        'label'=>'Test Result',
                                        'route'=>'application/testresult'
                                    ],
                                    [
                                        'label'=>'Test Results',
                                        'route'=>'application/default',
                                        'module'=>'application',
                                        'controller'=>'test',
                                        'action'=>'testresults'
                                    ],
                                    [
                                        'label'=>'Download Statement',
                                        'route'=>'application/default',
                                        'module'=>'application',
                                        'controller'=>'test',
                                        'action'=>'statement'
                                    ]
                                ]
                            ],
                            [
                                'label'=>'Documents',
                                'route'=>'application/certificates',
                                'pages'=>[
                                    [
                                        'label'=>'View',
                                        'route'=>'application/certificate'
                                    ]
                                ]
                            ],

                            [
                                'label'=>'Downloads',
                                'route'=>'application/downloads',
                                'pages'=>[
                                    [
                                        'label'=>'View',
                                        'route'=>'application/download-list'
                                    ]
                                ]
                            ],
                            [
                                'label'=>'Homework',
                                'route'=>'application/assignments',
                                'pages'=>[
                                    [
                                        'label'=>'Submit Homework',
                                        'route'=>'application/submit-assignment'
                                    ],
                                    [
                                        'label'=>'Homework Submissions',
                                        'route'=>'application/assignment-submissions',
                                        'pages'=>[
                                            [
                                                'label'=>'Edit Homework',
                                                'route'=>'application/edit-assignment'
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            [
                                'label'=>'Student Forum',
                                'route'=>'application/default',
                                'controller'=>'forum',
                                'action'=>'index',
                                'pages'=>[
                                    [
                                        'label'=>'Forum Topics',
                                        'route'=>'application/default',
                                        'controller'=>'forum',
                                        'action'=>'topics'
                                    ]
                                ],
                            ],

                            [
                                'label'=>'Change Password',
                                'route'=>'application/password'
                            ]
                        ]
                    ),
                    array(
                        'label'=>'View Class',
                        'route'=>'class',
                    ),
                    array(
                        'label'=>'Article',
                        'route'=>'page',
                    ),
                    array(
                        'label'=>'Calendar',
                        'route'=>'calendar',
                    ),
                    array(
                        'label'=>'Upcoming Sessions',
                        'route'=>'sessions',
                        'pages'=>[

                            [
                                'label'=>'Session Details',
                                'route'=>'session-details',
                            ],

                        ]
                    ),
                    array(
                        'label'=>'Online Courses',
                        'route'=>'courses',
                        'pages'=>[

                            [
                                'label'=>'Course Details',
                                'route'=>'course-details',
                            ],

                        ]
                    ),
                    array(
                        'label'=>'Blog',
                        'route'=>'news',
                        'pages'=>[
                            [
                                'label'=>'View Post',
                                'route'=>'news-entry',
                            ]
                        ]
                    ),
                    array(
                        'label'=>'Login',
                        'route'=>'application/signin',
                        'pages'=>[
                            [
                                'label'=>'Register',
                                'route'=>'application/register'
                            ],
                            [
                                'label'=>'Change Password',
                                'route'=>'application/change-password'
                            ],
                            [
                                'label'=>'Reset Your Password',
                                'route'=>'application/reset'

                            ]
                        ]
                    ),
                    array(
                        'label'=>'Sessions',
                        'route'=>'application/enroll',

                    ),
                    array(
                        'label'=>'Dashboard',
                        'route'=>'admin/default',
                        'module'=>'admin',
                        'controller'=>'index',
                        'action'=>'index',
                        'pages'=>array(
                            [
                                'label'=>'Coupons',
                                'module'=>'admin',
                                'controller'=>'payment',
                                'action'=>'coupons',
                                'pages'=>[
                                    [
                                        'label'=>'Add Coupon',
                                        'module'=>'admin',
                                        'controller'=>'payment',
                                        'action'=>'addcoupon'
                                    ],
                                    [
                                        'label'=>'Edit Coupon',
                                        'module'=>'admin',
                                        'controller'=>'payment',
                                        'action'=>'editcoupon'
                                    ]
                                ]
                            ],
                            [
                                'label'=>'Currencies',
                                'module'=>'admin',
                                'controller'=>'setting',
                                'action'=>'currencies',
                                'pages'=>[
                                    [
                                        'label'=>'Edit Currency',
                                        'module'=>'admin',
                                        'controller'=>'setting',
                                        'action'=>'editcurrency'
                                    ]
                                ]
                            ],
                            [
                                'label'=>'Grades',
                                'module'=>'admin',
                                'controller'=>'setting',
                                'action'=>'testgrades',
                                'pages'=>[
                                    [
                                        'label'=>'Add Grade',
                                        'module'=>'admin',
                                        'controller'=>'setting',
                                        'action'=>'addtestgrade'
                                    ],
                                    [
                                        'label'=>'Edit Grade',
                                        'module'=>'admin',
                                        'controller'=>'setting',
                                        'action'=>'edittestgrade'
                                    ]
                                ]
                            ],
                            [
                              'label'=>'Reports',
                                'module'=>'admin',
                                'controller'=>'report',
                                'action'=>'index',
                                'pages'=>[
                                    [
                                        'label'=>'Class Report',
                                        'module'=>'admin',
                                        'controller'=>'report',
                                        'action'=>'classes',
                                    ],
                                    [
                                        'label'=>'Student Report',
                                        'module'=>'admin',
                                        'controller'=>'report',
                                        'action'=>'students',
                                    ],
                                    [
                                        'label'=>'Test Report',
                                        'module'=>'admin',
                                        'controller'=>'report',
                                        'action'=>'tests',
                                    ],
                                    [
                                        'label'=>'Homework Report',
                                        'module'=>'admin',
                                        'controller'=>'report',
                                        'action'=>'homework',
                                    ]
                                ]
                            ],
                            [
                              'label'=>'Student Forum',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'forum',
                                'action'=>'index',
                                'pages'=>[
                                    [
                                        'label'=>'Forum Topic',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'forum',
                                        'action'=>'topic'
                                    ],
                                    [
                                        'label'=>'Add Forum Topic',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'forum',
                                        'action'=>'addtopic'
                                    ]
                                ]
                            ],
                            array(
                                'label'=>'Change Email',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'account',
                                'action'=>'email',
                            ),
                            array(
                                'label'=>'Change Password',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'account',
                                'action'=>'password',
                            ),
                            array(
                                'label'=>'Profile',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'account',
                                'action'=>'profile',
                            ),
                            [
                                'label'=>'Instructor Chat',
                                'route'=>'admin/default',
                                'controller'=>'discuss',
                                'action'=>'index',
                                'pages'=>[
                                    [
                                        'label'=>'View Chat',
                                        'route'=>'admin/default',
                                        'controller'=>'discuss',
                                        'action'=>'viewdiscussion',
                                    ],

                                ]
                            ],

                            array(
                                'label'=>'Students',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'student',
                                'action'=>'index',
                                'pages'=>array(
                                    array(
                                        'label'=>'Edit',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'student',
                                        'action'=>'edit',
                                    ),
                                    array(
                                        'label'=>'Add',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'student',
                                        'action'=>'add',
                                    )

                                )


                            ),
                            array(
                                'label'=>'Import/Export',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'student',
                                'action'=>'import',
                            ),
                            array(
                                'label'=>'Transactions',
                                'route'=>'admin/default',
                                'module'=>'application',
                                'controller'=>'student',
                                'action'=>'transactions'
                            ),
                            array(
                                'label'=>'Payments',
                                'route'=>'admin/payments',
                            ),
                            array(
                                'label'=>'Course Categories',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'session',
                                'action'=>'groups',
                                'pages'=>[
                                    [
                                        'label'=>'Add Category',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'session',
                                        'action'=>'addgroup',
                                    ],
                                    [
                                        'label'=>'Edit Category',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'session',
                                        'action'=>'editgroup',
                                    ],
                                ]
                            ),
                            array(
                                'label'=>'Courses and Sessions',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'student',
                                'action'=>'sessions',
                                'pages'=>array(
                                    array(
                                        'label'=>'Edit Session',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'student',
                                        'action'=>'editsession',
                                    ),
                                    array(
                                        'label'=>'Add Session',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'student',
                                        'action'=>'addsession',
                                    ),
                                    array(
                                        'label'=>'Edit Course',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'session',
                                        'action'=>'editcourse',
                                    ),
                                    array(
                                        'label'=>'Add Course',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'session',
                                        'action'=>'addcourse',
                                    ),
                                    array(
                                        'label'=>'Manage Classes',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'session',
                                        'action'=>'sessionclasses',
                                    ),
                                    array(
                                        'label'=>'Manage Classes',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'session',
                                        'action'=>'courseclasses',
                                    ),
                                    array(
                                        'label'=>'Enrolled Students',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'student',
                                        'action'=>'sessionstudents',
                                    ),
                                    array(
                                        'label'=>'Send Mail',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'student',
                                        'action'=>'mailsession',
                                    ),
                                    array(
                                        'label'=>'Instructors',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'student',
                                        'action'=>'instructors',
                                    ),
                                    [
                                        'label'=>'Manage Tests',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'session',
                                        'action'=>'tests'
                                    ],
                                )

                            ),
                            array(
                                'label'=>'Attendance',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'student',
                                'action'=>'attendance',
                            ),
                            array(
                                'label'=>'Attendance Bulk',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'student',
                                'action'=>'attendancebulk',
                            ),
                            array(
                                'label'=>'Attendance Import',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'student',
                                'action'=>'attendanceimport',
                            ),
                            array(
                                'label'=>'Bulk Enroll',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'student',
                                'action'=>'massenroll',
                            ),
                            array(
                                'label'=>'Certificate List',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'student',
                                'action'=>'certificatelist',
                            ),

                            array(
                                'label'=>'Attendance Dates',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'student',
                                'action'=>'attendancedate',
                            ),

                            array(
                                'label'=>'Articles',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'articles',
                                'action'=>'index',
                                'pages'=>array(
                                    array(
                                        'label'=>'Edit',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'articles',
                                        'action'=>'edit',
                                    ),
                                    array(
                                        'label'=>'Add',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'articles',
                                        'action'=>'add',
                                    ),
                                )


                            ),
                            array(
                                'label'=>'Surveys',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'survey',
                                'action'=>'index',
                                'pages'=>array(
                                    [
                                        'label'=>'courses-sessions',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'survey',
                                        'action'=>'sessions'
                                    ],
                                    array(
                                        'label'=>'Results',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'survey',
                                        'action'=>'results',
                                    ),
                                    array(
                                        'label'=>'Edit',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'survey',
                                        'action'=>'edit',
                                    ),
                                    array(
                                        'label'=>'Add',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'survey',
                                        'action'=>'add',
                                    ),
                                    array(
                                        'label'=>'Survey Questions',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'survey',
                                        'action'=>'questions',
                                    ),

                                    array(
                                        'label'=>'Edit Question',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'survey',
                                        'action'=>'editquestion',
                                    ),
                                    [
                                        'label'=>'Survey Report',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'survey',
                                        'action'=>'report',
                                    ]
                                )


                            ),
                            array(
                                'label'=>'Tests',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'test',
                                'action'=>'index',
                                'pages'=>array(
                                    [
                                        'label'=>'courses-sessions',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'test',
                                        'action'=>'sessions'
                                    ],
                                    array(
                                        'label'=>'Results',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'test',
                                        'action'=>'results',
                                    ),
                                    array(
                                        'label'=>'Edit',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'test',
                                        'action'=>'edit',
                                    ),
                                    array(
                                        'label'=>'Add',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'test',
                                        'action'=>'add',
                                    ),
                                    array(
                                        'label'=>'Test Questions',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'test',
                                        'action'=>'questions',
                                    ),

                                    array(
                                        'label'=>'Edit Question',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'test',
                                        'action'=>'editquestion',
                                    ),
                                )


                            ),
                            array(
                                'label'=>'Blog Posts',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'news',
                                'action'=>'index',
                                'pages'=>array(
                                    array(
                                        'label'=>'Edit',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'news',
                                        'action'=>'edit',
                                    ),
                                    array(
                                        'label'=>'Add',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'news',
                                        'action'=>'add',
                                    ),
                                )


                            ),
                            [
                                'label'=>'Video Library',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'video',
                                'action'=>'index',
                                'pages'=>[
                                    array(
                                        'label'=>'Edit Video',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'video',
                                        'action'=>'edit',
                                    ),
                                    array(
                                        'label'=>'Add Video',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'video',
                                        'action'=>'add',
                                    ),
                                    array(
                                        'label'=>'Disk Space Usage',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'video',
                                        'action'=>'disk',
                                    ),
                                ]
                            ]
                            ,
                            array(
                                'label'=>'Classes',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'lesson',
                                'action'=>'index',
                                'pages'=>array(
                                    array(
                                        'label'=>'Edit',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'lesson',
                                        'action'=>'edit',
                                    ),
                                    array(
                                        'label'=>'Add',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'lesson',
                                        'action'=>'add',
                                    ),
                                    array(
                                        'label'=>'Class Downloads',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'lesson',
                                        'action'=>'files',
                                    ),
                                    array(
                                        'label'=>'Class Lectures',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'lecture',
                                        'action'=>'index',
                                        'pages'=>[
                                            [
                                                'label'=>'Lecture Content',
                                                'route'=>'admin/default',
                                                'module'=>'admin',
                                                'controller'=>'lecture',
                                                'action'=>'content',

                                            ]
                                        ]
                                    ),
                                )


                            ),
                            array(
                                'label'=>'Class Groups',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'lesson',
                                'action'=>'groups',
                                'pages'=>array(
                                    array(
                                        'label'=>'Edit Group',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'lesson',
                                        'action'=>'editgroup',
                                    ),
                                    array(
                                        'label'=>'Add Group',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'lesson',
                                        'action'=>'addgroup',
                                    ),
                                )


                            ),
                            array(
                                'label'=>'Certificates',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'certificate',
                                'action'=>'index',
                                'pages'=>array(
                                    array(
                                        'label'=>'Edit',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'certificate',
                                        'action'=>'edit',
                                    ),
                                    array(
                                        'label'=>'Add',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'certificate',
                                        'action'=>'add',
                                    ),
                                    array(
                                        'label'=>'Student Downloads',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'certificate',
                                        'action'=>'students',
                                    ),
                                    array(
                                        'label'=>'Track Certificate',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'certificate',
                                        'action'=>'track',
                                    ),
                                )


                            ),
                            array(
                                'label'=>'Downloads',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'download',
                                'action'=>'index',
                                'pages'=>array(
                                    array(
                                        'label'=>'Edit',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'download',
                                        'action'=>'edit',
                                    ),
                                    array(
                                        'label'=>'Add',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'download',
                                        'action'=>'add',
                                    ),
                                )


                            ),
                            array(
                                'label'=>'Themes',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'template',
                                'action'=>'index',
                                'pages'=>[
                                    [
                                        'label'=>'Customize Theme',
                                        'route'=> 'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'template',
                                        'action'=>'customize',

                                    ]
                                ]
                            ),
                            array(
                                'label'=>'Site Settings',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'setting',
                                'action'=>'index',
                            ),
                            array(
                                'label'=>'Language',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'setting',
                                'action'=>'language',
                            ),
                            array(
                                'label'=>'SMS Setup',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'smsgateway',
                                'action'=>'index',
                                'pages'=>[
                                    [
                                        'label'=>'Configure Gateway',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'smsgateway',
                                        'action'=>'customize',
                                    ]
                                ]
                            ),
                            array(
                                'label'=>'Email Notifications',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'messages',
                                'action'=>'emails',
                                'pages'=>[
                                    [
                                        'label'=>'Edit Email Notification',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'messages',
                                        'action'=>'editemail',
                                    ]
                                ]
                            ),
                            array(
                                'label'=>'SMS Notifications',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'messages',
                                'action'=>'sms',
                                'pages'=>[
                                    [
                                        'label'=>'Edit SMS Notification',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'messages',
                                        'action'=>'editsms',
                                    ]
                                ]
                            ),
                            array(
                                'label'=>'Upgrade Database',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'setting',
                                'action'=>'migrate',
                            ),
                            array(
                                'label'=>'Administrators',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'setting',
                                'action'=>'admins',
                                'pages'=>[
                                    [
                                        'label'=>'Add Administrator',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'setting',
                                        'action'=>'addadmin',
                                    ],
                                    [
                                        'label'=>'Edit Administrator',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'setting',
                                        'action'=>'editadmin',
                                    ]
                                ]
                            ),
                            array(
                                'label'=>'Roles',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'setting',
                                'action'=>'roles',
                                'pages'=>[
                                    [
                                        'label'=>'Add Role',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'setting',
                                        'action'=>'addrole',
                                    ],
                                    [
                                        'label'=>'Edit Role',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'setting',
                                        'action'=>'editrole',
                                    ]
                                ]
                            ),
                            array(
                                'label'=>'Custom Student Fields',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'setting',
                                'action'=>'fields',
                                'pages'=>array(
                                    array(
                                        'label'=>'Edit',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'setting',
                                        'action'=>'editfield',
                                    ),
                                    array(
                                        'label'=>'Add',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'setting',
                                        'action'=>'addfield',
                                    ),
                                )


                            ),
                            array(
                                'label'=>'Homepage Widgets',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'widget',
                                'action'=>'index',

                            ),
                            array(
                                'label'=>'Payment Methods',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'payment',
                                'action'=>'index',
                                'pages'=>array(
                                    array(
                                        'label'=>'Edit',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'payment',
                                        'action'=>'edit',
                                    )
                                )


                            ),
                            array(
                                'label'=>'Revision Notes',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'homework',
                                'action'=>'index',
                                'pages'=>array(
                                    array(
                                        'label'=>'Edit',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'homework',
                                        'action'=>'edit',
                                    ),
                                    array(
                                        'label'=>'Add',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'homework',
                                        'action'=>'add',
                                    ),
                                )


                            ),
                            array(
                                'label'=>'Homework',
                                'route'=>'admin/default',
                                'module'=>'admin',
                                'controller'=>'assignment',
                                'action'=>'index',
                                'pages'=>array(
                                    array(
                                        'label'=>'Edit',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'assignment',
                                        'action'=>'edit',
                                    ),
                                    array(
                                        'label'=>'Add',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'assignment',
                                        'action'=>'add',
                                    ),
                                    array(
                                        'label'=>'Submissions',
                                        'route'=>'admin/default',
                                        'module'=>'admin',
                                        'controller'=>'assignment',
                                        'action'=>'submissions',
                                        'pages'=>[
                                            [
                                                'label'=>'View Submission',
                                                'route'=>'admin/default',
                                                'module'=>'admin',
                                                'controller'=>'assignment',
                                                'action'=>'viewsubmission',
                                            ]
                                        ]
                                    )
                                )


                            ),

                        )
                    ),
                    array(
                        'label'=>'Features',
                        'route'=>'features'
                    ),
                    array(
                        'label'=>'Pricing',
                        'route'=>'pricing'
                    ),
                    array(
                        'label'=>'Showcase',
                        'route'=>'showcase'
                    ),
                    array(
                        'label'=>'Faq',
                        'route'=>'faq'
                    ),
                    array(
                        'label'=>'About',
                        'route'=>'about'
                    ),
                    array(
                        'label'=>'Contact Us',
                        'route'=>'contact'
                    ),
                    array(
                        'label'=>'Signup',
                        'route'=>'signup'
                    ),
                    array(
                        'label'=>'Login',
                        'route'=>'login'
                    ),
                    array(
                        'label'=>'Privacy Policy',
                        'route'=>'privacy'
                    ),
                    array(
                        'label'=>'Terms Of Service',
                        'route'=>'terms'
                    ),
                    array(
                        'label' => 'Login',
                        'route' => 'merchant/signin',
                        'pages'=>array(
                            array(
                                'Label'=>'Password Reset',
                                'route'=>'forgot'
                            )
                        )

                    ),
                    array(
                        'label' => 'Login',
                        'route' => 'merchant/process',

                    ),

                ),
            ),

        )
    ),
    'session' => [
        'config' => [
            'class' => Session\Config\SessionConfig::class,
            'options' => [
                'name'=>'traineasy',
                // Session cookie will expire in 30 days.
                'cookie_lifetime' => 60*60*24*30,
                // Session data will be stored on server maximum for 30 days.
                'gc_maxlifetime'     => 60*60*24*30,
                'remember_me_seconds'=> 60*60*24*90,
                'cookie_secure'=>false
            ],
        ],
        'storage' => Session\Storage\SessionArrayStorage::class,
        'save_handler'=> 'DBSaveHandler'

    ],
    /*
       'session_config' => [
           'name'=>'traineasy',
           // Session cookie will expire in 30 days.
           'cookie_lifetime' => 60*60*24*30,
           // Session data will be stored on server maximum for 30 days.
           'gc_maxlifetime'     => 60*60*24*30,
           'remember_me_seconds'=> 60*60*24*90,
           'cookie_see'=>false
       ],
        /*
       // Session manager configuration.
       'session_manager' => [
           // Session validators (used for security).

           'validators' => [
         //      RemoteAddr::class,
          //     HttpUserAgent::class,
           ]

       ],
       // Session storage configuration.
       'session_storage' => [
           'type' => SessionArrayStorage::class
       ],
       */

    'san_captcha' => [
        'class' => 'image',
        'options' => [
            'width' => 200,
            'height' => 70,
            'dotNoiseLevel' => 15,
            'lineNoiseLevel' => 2,
            "suffix" => ""
        ],
    ],
    's3'=>[
        'region'=>'',
        'bucket'=>'',
        'key'=>'',
        'secret'=>'',
        'cloudfront_domain'=> '',
        'cloudfront_key_pair_id'=>''
    ]
];
