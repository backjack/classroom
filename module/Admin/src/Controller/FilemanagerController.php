<?php

namespace Admin\Controller;

use Application\Controller\AbstractController;
use Intermatics\HelperTrait;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
error_reporting(0);
/**
 * FilemanagerController
 *
 * @author
 *
 * @version
 *
 */
class FilemanagerController extends AbstractController {
    use HelperTrait;
    private $data = [];
    private $filePath;
    private $fileUrl;
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
        $this->setFilePath();
        $user= '';
        if(defined('USER_ID')){
            $user = '/'.USER_ID;
        }
		// TODO Auto-generated IndexController::indexAction() default action
		$this->data['pageTitle'] = 'File Manager';
		 $this->layout('layout/filemanager2');
		 $this->data['directory'] = $this->fileUrl;

		 if (isset($_GET['field'])) {
		 	$this->data['field'] = $_GET['field'];
		 } else {
		 	$this->data['field'] = '';
		 }

         $this->data['siteUrl'] = $this->getBaseUrl();

		 return new ViewModel ($this->data);
	}

    private function setFilePath(){

        $user= '';
        if(defined('USER_ID')){
            $user = '/'.USER_ID;
        }
        $filePath = 'public/usermedia'.$user;
        $fileUrl = $this->getBaseUrl() . '/usermedia'.$user;
        if(!$this->hasPermission('misc/global_access')){

            if(!is_dir(USER_PATH.'/admin_files')){
                mkdir(USER_PATH.'/admin_files') or die('unable to make folder');
            }

            $fileUrl = $fileUrl.'/admin_files/'.$this->getAdminId();
            $filePath = $filePath.'/admin_files/'.$this->getAdminId();
            if(!is_dir($filePath)){
                mkdir($filePath) or die('unable to create subdirectory');
            }

        }

        $this->filePath = $filePath;
        $this->fileUrl = $fileUrl;


    }

	public function connectorAction()
	{

		$dir = 'public/filemanager/php/';
		include_once $dir.'elFinderConnector.class.php';
		include_once $dir.'elFinder.class.php';
		include_once $dir.'elFinderVolumeDriver.class.php';
		include_once $dir.'elFinderVolumeLocalFileSystem.class.php';
		// Required for MySQL storage connector
		// include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeMySQL.class.php';
		// Required for FTP connector support
		// include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeFTP.class.php';


		/**
		 * Simple function to demonstrate how to control file access using "accessControl" callback.
		 * This method will disable accessing files/folders starting from  '.' (dot)
		 *
		 * @param  string  $attr  attribute name (read|write|locked|hidden)
		 * @param  string  $path  file path relative to volume root directory started with directory separator
		 * @return bool|null
		 **/
		function access($attr, $path, $data, $volume) {
			return strpos(basename($path), '.') === 0       // if file/folder begins with '.' (dot)
			? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
			:  null;                                    // else elFinder decide it itself
		}





        function logger($cmd, $result, $args, $elfinder) {
            $log = sprintf('[%s] %s:', date('r'), strtoupper($cmd));
            foreach ($result as $key => $value) {
                if (empty($value)) {
                    continue;
                }
                $data = array();
                if (in_array($key, array('error', 'warning'))) {
                    array_push($data, implode(' ', $value));
                } else {
                    if (is_array($value)) { // changes made to files
                        foreach ($value as $file) {
                            $filepath = (isset($file['realpath']) ? $file['realpath'] : $elfinder->realpath($file['hash']));
                            array_push($data, $filepath);
                        }
                    } else { // other value (ex. header)
                        array_push($data, $value);
                    }
                }
                $log .= sprintf(' %s(%s)', $key, implode(', ', $data));
            }
            $log .= "\n";

            $logfile = '../files/temp/log.txt';
            $dir = dirname($logfile);
            if (!is_dir($dir) && !mkdir($dir)) {
                return;
            }
            if (($fp = fopen($logfile, 'a'))) {
                fwrite($fp, $log);
                fclose($fp);
            }
        }
        //set custom page
        $this->setFilePath();

		$opts = array(
				'debug' => true,
                'bind' => array(
                    'mkdir mkfile duplicate upload rm paste rename' => 'sanitizeFile'
                ),
				'roots' => array(
						array(
								'driver'        => 'LocalFileSystem',   // driver for accessing file system (REQUIRED)
								'path'          => $this->filePath,         // path to files (REQUIRED)
								'URL'           => $this->fileUrl , // URL to files (REQUIRED)
							//	'accessControl' => 'access',             // disable and hide dot starting files (OPTIONAL)
							//	'mimeDetect'    => 'mime_content_type',
								'uploadMaxSize' => '10M',
								'attributes' => array(
										array(
												'pattern' => '/\.tmb|\.ph|\.htaccess/', //You can also set permissions for file types by adding, for example, .jpg inside pattern.
												'read'    => false,
												'write'   => false,
												'locked'  => true,
												'hidden' => true
										),
										array(
												'pattern' => '/\.jpg|\.mp4|\.mp3|\.avi|\.xls|\.7z|\.mdb|\.mdbx|\.csv|\.xlsx|\.txt|\.zip|\.doc|\.docx|\.pptx|\.pdf|\.ppt|\.png|\.gif|\.jpeg/', //You can also set permissions for file types by adding, for example, .jpg inside pattern.
												'read'    => true,
												'write'   => true,
												'locked'  => false,
												'hidden' => false
										),

								),
								'uploadAllow' => array('image','application/pdf','application/zip','application/x-compressed','application/x-zip-compressed','text/plain','audio/mpeg3','video/mpeg','application/x-troff-msvideo','video/msvideo','application/excel','application/vnd.ms-excel','application/x-msexcel','video/mp4',
                                'application/msaccess','application/x-msaccess','application/vnd.msaccess','application/csv','text/csv','application/msword','application/mspowerpoint','application/x-mspowerpoint','application/powerpoint','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.template','application/vnd.openxmlformats-officedocument.presentationml.presentation','application/vnd.openxmlformats-officedocument.presentationml.template'
                                ),
								'uploadDeny' => array('all'),
								'uploadOrder' => 'deny,allow'
														)
				)
		);

		// run elFinder
		$connector = new \elFinderConnector(new \elFinder($opts));
		$connector->run();
		exit();
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


	public function imageAction() {
		 
 
		if (isset($_GET['image'])) {
	
			exit(resizeImage(html_entity_decode(urldecode($_GET['image']), ENT_QUOTES, 'UTF-8'), 100, 100,$this->getBaseUrl()));
		}
	}

}