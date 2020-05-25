<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 8/22/2017
 * Time: 11:11 AM
 */

namespace Application\Controller;

use Application\Model\DownloadFileTable;
use Application\Model\DownloadSessionTable;
use Application\Model\DownloadTable;
use Application\Model\StudentSessionTable;
use Intermatics\HelperTrait;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class DownloadController extends AbstractController {

    use HelperTrait;
    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);
        $controller = $this;
        $events->attach('dispatch', function ($e) use ($controller) {
            $controller->layout('layout/student');
        }, 100);
    }
    public function indexAction(){

        $table = new DownloadTable($this->getServiceLocator());
        $downloadFileTable = new DownloadFileTable($this->getServiceLocator());
        $downloadSessionTable = new DownloadSessionTable($this->getServiceLocator());
        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());

        $paginator = $studentSessionTable->getDownloads($this->getId());

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);
        return new ViewModel(array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Downloads'),
            'downloadTable'=>$table,
            'fileTable'=>$downloadFileTable,
            'sessionTable' => $downloadSessionTable,
            'studentId'=>$this->getId()
        ));

    }

    public function filesAction(){
        $downloadTable = new DownloadTable($this->getServiceLocator());
        $id = $this->params('id');
        $row = $downloadTable->getDownload($id,$this->getId());
        if(!$row){
            $this->flashMessenger()->addMessage(__('no-download-permission'));
            $this->redirect()->toRoute('application/downloads');
        }
        $table = new DownloadFileTable($this->getServiceLocator());
        $rowset = $table->getDownloadRecords($id);
        $viewModel = new ViewModel(['rowset'=>$rowset,'pageTitle'=>__('File List').': '.$row->download_name,'id'=>$id,'row'=>$row]);

        return $viewModel;
    }


    public function fileAction(){
        set_time_limit(86400);
        $id = $this->params('id');
        $table = new DownloadFileTable($this->getServiceLocator());
        $row = $table->getFile($id,$this->getId());
        $path = 'public/usermedia/'.$row->path;



        header('Content-type: '.getFileMimeType($path));

// It will be called downloaded.pdf
        header('Content-Disposition: attachment; filename="'.basename($path).'"');

// The PDF source is in original.pdf
        readfile($path);
        exit();
    }


    public function allfilesAction(){
        set_time_limit(86400);
        $id = $this->params('id');
        $downloadTable = new DownloadTable($this->getServiceLocator());
        $downloadFileTable= new DownloadFileTable($this->getServiceLocator());
        $rowset = $downloadFileTable->getFiles($id,$this->getId());
        $downloadRow = $downloadTable->getRecord($id);

        $zipname = safeUrl($downloadRow->download_name).'.zip';
        $zip = new \ZipArchive;
        $zip->open($zipname, \ZipArchive::CREATE);
        $count = 1;
        $deleteFiles = [];
        foreach ($rowset as $row) {
            $path = 'public/usermedia/' . $row->path;

            if (file_exists($path)) {
            $newFile = $count.'-'.basename($path);
                copy($path,$newFile);
            $zip->addFile($newFile);
            $count++;
                $deleteFiles[] = $newFile;
             }



        }
        $zip->close();

        foreach($deleteFiles as $value){
            unlink($value);
        }

        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename='.$zipname);
        header('Content-Length: ' . filesize($zipname));
        readfile($zipname);
        unlink($zipname);
        exit();
    }

}