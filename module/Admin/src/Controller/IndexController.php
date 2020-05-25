<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Admin for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Application\Controller\AbstractController;
use Application\Model\LessonTable;
use Application\Model\SessionTable;
use Application\Model\StudentSessionTable;
use Application\Model\StudentTable;
use Application\Model\TestTable;
use Zend\Mvc\Controller\AbstractActionController;

class IndexController extends AbstractController
{

    public function indexAction()
    {
        $output = [];

        //get total students
        $studentsTable = new StudentSessionTable($this->getServiceLocator());
        $output['totalStudents'] = $studentsTable->getTotalActiveStudents();

        $sessionTable = new SessionTable($this->getServiceLocator());
        $output['totalSessions'] = $sessionTable->getPaginatedRecords(false,null,true,null,null,null,['s','b'],true)->count();
        $output['totalCourses'] = $sessionTable->getPaginatedRecords(false,null,true,null,null,null,'c')->count();

        $lessonTable = new LessonTable($this->getServiceLocator());
        $output['totalClasses'] = $lessonTable->getTotal();

        $testTable = new TestTable($this->getServiceLocator());
        $output['totalTests'] = $testTable->getActivePaginatedRecords()->count();

        $viewModel = $this->forward()->dispatch('Admin\Controller\Student',['action'=>'payments']);
        $output['payment'] = $viewModel->getVariables();
        $output['payment']['paginator']->setItemCountPerPage(5);

        $viewModel = $this->forward()->dispatch('Admin\Controller\Student');
        $output['student'] = $viewModel->getVariables();
        $output['student']['paginator']->setItemCountPerPage(5);

        $viewModel = $this->forward()->dispatch('Admin\Controller\Student',['action'=>'sessions']);
        $output['session'] = $viewModel->getVariables();
        $output['session']['paginator']->setItemCountPerPage(5);

        $viewModel = $this->forward()->dispatch('Admin\Controller\Assignment');
        $output['assignment'] = $viewModel->getVariables();
        $output['assignment']['paginator']->setItemCountPerPage(5);

        $_GET['replied'] = 0;
        $viewModel = $this->forward()->dispatch('Admin\Controller\Discuss');
        $output['discuss'] = $viewModel->getVariables();
        $output['discuss']['paginator']->setItemCountPerPage(5);

        $output['pageTitle'] = __('Dashboard');
        $output['hideCrumbs'] = true;
        return $output;
       //$viewModel = $this->forward()->dispatch('Admin/Controller/Student');
       //return $viewModel;
    }

    public function fooAction()
    {
    	 
        // This shows the :controller and :action parameters in default route
        // are working when you browse to /index/index/foo
        return array();
    }

    public function  nopermissionAction()
    {
        return ['pageTitle'=>__('Permission Error')];
    }

    public function phinxAction()
    {
        $config = $this->getServiceLocator()->get('config');
        $_SERVER['PHINX_DBHOST'] = $config['db']['hostname'];
        $_SERVER['PHINX_DBNAME'] = $config['db']['dbname'];
        $_SERVER['PHINX_DBUSER'] = $config['db']['username'];
        $_SESSION['PHINX_DBPASS']= $config['db']['password'];

        $app = require  'vendor/robmorgan/phinx/app/phinx.php';
        $wrap = new \Phinx\Wrapper\TextWrapper($app);

// Mapping of route names to commands.
        $routes = [
            'status'   => 'getStatus',
            'migrate'  => 'getMigrate',
            'rollback' => 'getRollback',
        ];

// Extract the requested command from the URL, default to "status".
     //   $command = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $command=$_GET['command'];
        if (!$command) {
            $command = 'status';
        }

// Verify that the command exists, or list available commands.
        if (!isset($routes[$command])) {
            $commands = implode(', ', array_keys($routes));
            header('Content-Type: text/plain', true, 404);
            die("Command not found! Valid commands are: {$commands}.");
        }

// Get the environment and target version parameters.
        $env    = isset($_GET['e']) ? $_GET['e'] : null;
        $target = isset($_GET['t']) ? $_GET['t'] : null;

// Check if debugging is enabled.
        $debug  = !empty($_GET['debug']) && filter_var($_GET['debug'], FILTER_VALIDATE_BOOLEAN);

// Execute the command and determine if it was successful.
        $output = call_user_func([$wrap, $routes[$command]], $env, $target);
        $error  = $wrap->getExitCode() > 0;

// Finally, display the output of the command.
        header('Content-Type: text/plain', true, $error ? 500 : 200);
        if ($debug) {
            // Show what command was executed based on request parameters.
            $args = implode(', ', [var_export($env, true), var_export($target, true)]);
            echo "DEBUG: $command($args)" . PHP_EOL . PHP_EOL;
        }
        echo $output;

    }

    public function enrollAction(){
        set_time_limit(0);
        $attendanceTable = new \Application\Model\AttendanceTable($this->getServiceLocator());
        $studentSessionTable = new \Application\Model\StudentSessionTable($this->getServiceLocator());
        $studentTable = new StudentTable($this->getServiceLocator());
        $sessionTable = new SessionTable($this->getServiceLocator());

        //get all sessions
        $sessions = $sessionTable->getRecords();
        foreach($sessions as $session){

            //get all students from Attendance table for this session
            $select = new \Zend\Db\Sql\Select('attendance');
            $select->where(['session_id'=>$session->session_id]);
            $select->group('student_id');
            $students = $attendanceTable->tableGateway->selectWith($select);
            //loop all students
            foreach($students as $student){

                if(!$studentSessionTable->enrolled($student->student_id,$session->session_id)){
                    $code = generateRandomString(5);
                    $studentSessionTable->addRecord(array(
                        'student_id'=>$student->student_id,
                        'session_id'=>$session->session_id,
                        'reg_code'=>$code
                    ));
                }
            }

        }

        exit('Operation complete');


    }
}
