<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 12/12/2018
 * Time: 10:40 AM
 */

namespace Application\Slim\V1\Controller;

use Application\Entity\Newsflash;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

class BlogController   extends  Controller {


    public function posts(Request $request,Response $response,$args){

        $params = $request->getQueryParams();
        if(isset($params['rows']) && !empty($params['rows']) && $params['rows'] <= 100 ){
            $rowsPerPage = $params['rows'];
        }
        else{
            $rowsPerPage = 30;
        }

        $select = Newsflash::orderBy('newsflash_id','desc');

        if(isset($params['filter']) && !empty($params['filter'])){
            $filter = $params['filter'];
            $select->whereRaw("MATCH (title,content) AGAINST ('$filter' IN NATURAL LANGUAGE MODE)",[$filter]);
        }

        $data['total'] = $select->count();
        $rowset = $select->paginate(30);
        $data = [];

       // $data['current_page']=(int) (empty($params['page'])? 1 : $params['page']);
//        $data['rows_per_page'] = $rowsPerPage;
        $data += $rowset->toArray();

        $newData = [];
        foreach($data['data'] as $row){
            $row['content'] = strip_tags(limitLength($row['content'],200));
            $row['date']= date('d M Y',$row['date']);
            $newData[]  = $row;
        }

        $data['data'] = $newData;


        return jsonResponse($data);
    }

    public function getPost(Request $request,Response $response,$args){

        $id = $args['id'];
        $row = Newsflash::find($id);
        $data = $row->toArray();
        $data['date'] = date('d M Y',$data['date']);
        return jsonResponse($data);
    }

}