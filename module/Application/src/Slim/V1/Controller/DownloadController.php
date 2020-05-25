<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 11/22/2018
 * Time: 11:01 AM
 */

namespace Application\Slim\V1\Controller;


use Application\Entity\Download;
use Application\Model\DownloadFileTable;
use Application\Model\DownloadSessionTable;
use Application\Model\DownloadTable;

use Application\Model\LessonFileTable;
use Application\Model\StudentSessionTable;
use Intermatics\HelperTrait;
use Intermatics\UtilityFunctions;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class DownloadController  extends Controller {

    use HelperTrait;


    public function downloads(Request $request,Response $response,$args){

        $table = new DownloadTable();
        $downloadFileTable = new DownloadFileTable();
        $downloadSessionTable = new DownloadSessionTable();
        $studentSessionTable = new StudentSessionTable();

        $paginator = $studentSessionTable->getDownloads($this->getApiStudent()->student_id);
        $params = $request->getQueryParams();

        $perPage= 30;
        $page = empty($params['page'])? 1:$params['page'];
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($perPage);

        $output = [];

        $output['per_page'] = $perPage;
        $output['total'] = $studentSessionTable->getDownloadsTotal($this->getApiStudentId());
        $output['current_page']= $page;

        $totalPages= ceil($output['total'] /$perPage);
        $downloadRows = [];
        if($page<=$totalPages){
            foreach($paginator as $row){

                $dRow= Download::find($row->download_id);
                $downloadRows[] = [
                  'download_id'=>$row->download_id,
                    'download_name'=>$row->download_name,
                    'description'=>$dRow->description,
                    'files'=>$downloadFileTable->getTotalForDownload($row->download_id),
                ];
            }
        }

        $output['data'] = $downloadRows;
        return jsonResponse($output);



    }

    public function getDownload(Request $request,Response $response,$args){
        $downloadTable = new DownloadTable();
        $id = $args['id'];
        $student = $this->getApiStudent();

        $row = $downloadTable->getDownload($id,$student->student_id);
        if(!$row){
             return jsonResponse([
               'status'=>false,
                'message'=>'You do not have permission to access this download'
            ]);
        }

        $download = Download::find($id)->toArray();

        $table = new DownloadFileTable();
        $rowset = $table->getDownloadRecords($id);

        $download['files']= $rowset->toArray();

        return jsonResponse([
            'status'=>true,
            'download'=>$download
        ]);

    }



    public function file(Request $request,Response $response,$args){
        set_time_limit(86400);
        $id = $args['id'];
        $table = new DownloadFileTable();
        $row = $table->getFile($id,$this->getApiStudent()->student_id);
        $path = 'public/usermedia/'.$row->path;
        header('Content-type: '.getFileMimeType($path));
        header('Content-Disposition: attachment; filename="'.basename($path).'"');
        readfile($path);
        exit();
    }



}