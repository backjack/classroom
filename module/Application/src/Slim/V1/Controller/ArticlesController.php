<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 12/7/2018
 * Time: 2:11 PM
 */

namespace Application\Slim\V1\Controller;

use Application\Entity\Article;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

class ArticlesController extends  Controller {

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function articles(Request $request,Response $response,$args){

        $rowset = Article::where(function($query){
            $query->where('visibility','m')->orWhere('visibility','b');
        })->orderBy('sort_order')->get();

        $data = $rowset->toArray();
        return jsonResponse($data);
    }

    public function getArticle(Request $request,Response $response,$args){

        $id = $args['id'];
        $row = Article::find($id);
        $data = $row->toArray();
        return jsonResponse($data);
    }

}