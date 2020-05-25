<?php
namespace Application\Slim\V1\Middleware;
use Application\Entity\Student;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class StudentMiddleware {

    public function __invoke(Request $request,Response $response, $next)
    {

        $authToken = trim($request->getHeaderLine('Authorization'));

        //get user
        $student = Student::where('api_token',$authToken)->where('token_expires','>',time())->first();
        if(!$student){
            $response->withStatus(401);
            return jsonResponse(['status'=>false,'error'=>'Unauthorized request: '.$authToken]);
        }


        $response = $next($request, $response);

        return $response;
    }
}