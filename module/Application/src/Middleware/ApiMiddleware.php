<?php
namespace Application\Middleware;


use Application\Entity\Student;
use Application\Model\StudentTable;
use Application\Slim\V1\Controller\AppController;
use Application\Slim\V1\Controller\ArticlesController;
use Application\Slim\V1\Controller\AssignmentsController;
use Application\Slim\V1\Controller\BlogController;
use Application\Slim\V1\Controller\CertificateController;
use Application\Slim\V1\Controller\DiscussionController;
use Application\Slim\V1\Controller\DownloadController;
use Application\Slim\V1\Controller\ForumController;
use Application\Slim\V1\Controller\InvoiceController;
use Application\Slim\V1\Controller\RevisionNotesController;
use Application\Slim\V1\Controller\TestController;
use Application\Slim\V1\Middleware\StudentMiddleware;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ResponseInterface as Response;
//use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;
use Application\Slim\V1\Controller\StudentController;
use Application\Slim\V1\Controller\SessionController;

class ApiMiddleware implements MiddlewareInterface {

    public function process(Request $request, DelegateInterface $delegate)
    {
        $config = [];
        $mode= getenv('APP_MODE');
        if($mode!='demo'){
            $exceptions = false;
        }
        else{
            $exceptions = true;
        }

        $config['displayErrorDetails'] = $exceptions;
       // $config['addContentLengthHeader'] = false;
        $config["determineRouteBeforeAppMiddleware"]= true;

        $app = new App(['settings' => $config]);

        $app->options('/{routes:.+}', function ($request, $response, $args) {
            return jsonResponse($response);
        });

        $app->add(function ($req, $res, $next) {
            $response = $next($req, $res);
            return $response
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');

        });

        $app->group('/api/v1', function () {
            //authentication
            $this->post('/accounts',AppController::class.':login');
            $this->put('/accounts',AppController::class.':update');
            $this->get('/configs',AppController::class.':setup');
            $this->post('/students',StudentController::class.':create');
            $this->get('/sessions',SessionController::class.':getSessions');
            $this->get('/courses',SessionController::class.':getCourses');
            $this->get('/courses/{id}',SessionController::class.':getSession');
            $this->get('/sessions/{id}',SessionController::class.':getSession');
            $this->get('/articles',ArticlesController::class.':articles');
            $this->get('/articles/{id}',ArticlesController::class.':getArticle');

            $this->get('/posts',BlogController::class.':posts');
            $this->get('/posts/{id}',BlogController::class.':getPost');

            $this->get('/tokens/{id}',StudentController::class.':getToken');
            $this->get('/videos/{id}/index.m3u8',SessionController::class.':getVideo');

            $this->post('/password-resets',StudentController::class.':resetPassword');

            //This is the group of authorized routes
            $this->group('',function(){

                $this->post('/student-passwords',StudentController::class.':changePassword');



                $this->get('/students/{id}',StudentController::class.':getProfile');
                $this->put('/students/{id}',StudentController::class.':updateProfile');
                $this->post('/profile-photos',StudentController::class.':saveProfilePhoto');
                $this->delete('/profile-photos',StudentController::class.':removeProfilePhoto');

                $this->get('/intros/{id}',SessionController::class.':getIntro');
                $this->get('/classes/{id}',SessionController::class.':getClass');
                $this->get('/lectures/{id}',SessionController::class.':getLecture');

                $this->get('/invoices',InvoiceController::class.':invoices');
                $this->post('/invoices',InvoiceController::class.':storeInvoice');
                $this->get('/invoices/{id}',InvoiceController::class.':getInvoice');

                $this->get('/payment-methods',InvoiceController::class.':paymentMethods');
                $this->get('/payment-methods/{id}',InvoiceController::class.':getPaymentMethod');
                $this->get('/student-courses',SessionController::class.':studentCourses');
                $this->get('/bookmarks',SessionController::class.':bookmarks');
                $this->post('/bookmarks',SessionController::class.':storeBookmark');
                $this->delete('/bookmarks/{id}',SessionController::class.':removeBookmark');
                $this->get('/tests',TestController::class.':tests');
                $this->get('/tests/{id}',TestController::class.':getTest');
                $this->get('/test-results',TestController::class.':results');
                $this->get('/test-results/{id}',TestController::class.':getResult');
                $this->get('/student-tests',TestController::class.':studentTests');
                $this->get('/student-tests/{id}',TestController::class.':getStudentTest');
                $this->post('/student-tests',TestController::class.':createStudentTest');
                $this->put('/student-tests/{id}',TestController::class.':updateStudentTest');

                $this->get('/certificates',CertificateController::class.':certificates');
                $this->get('/certificates/{id}',CertificateController::class.':getCertificate');
                $this->get('/downloads',DownloadController::class.':downloads');
                $this->get('/downloads/{id}',DownloadController::class.':getDownload');
                $this->get('/download-files/{id}',DownloadController::class.':file');

                $this->get('/class-files/{id}',SessionController::class.':classFile');
                $this->post('/student-session-logs',SessionController::class.':loglecture');
                $this->get('/lecture-files/{id}',SessionController::class.':lectureFile');

                $this->get('/assignments',AssignmentsController::class.':assignments');
                $this->get('/assignments/{id}',AssignmentsController::class.':getAssignment');
                $this->post('/assignment-submissions',AssignmentsController::class.':createSubmission');
                $this->put('/assignment-submissions/{id}',AssignmentsController::class.':updateSubmission');
                $this->delete('/assignment-submissions/{id}',AssignmentsController::class.':deleteSubmission');
                $this->get('/revision-notes-sessions',AssignmentsController::class.':revisionNotesSessions');
                $this->get('/revision-notes',AssignmentsController::class.':revisionNotes');
                $this->get('/revision-notes/{id}',AssignmentsController::class.':getRevisionNote');
                $this->get('/discussion-options',DiscussionController::class.':options');
                $this->get('/discussions',DiscussionController::class.':discussions');
                $this->get('/discussions/{id}',DiscussionController::class.':getDiscussion');
                $this->post('/discussions',DiscussionController::class.':createDiscussion');
                $this->delete('/discussions/{id}',DiscussionController::class.':deleteDiscussion');
                $this->post('/discussion-replies',DiscussionController::class.':createDiscussionReply');
                $this->get('/forum-sessions',ForumController::class.':forumSessions');
                $this->get('/forum-topics',ForumController::class.':forumTopics');
                $this->get('/forum-topics/{id}',ForumController::class.':getForumTopic');
                $this->post('/forum-topics',ForumController::class.':createForumTopic');
                $this->delete('/forum-topics/{id}',ForumController::class.':deleteForumTopic');
                $this->get('/forum-posts',ForumController::class.':getForumPosts');
                $this->post('/forum-posts',ForumController::class.':createForumPost');

            })->add(new StudentMiddleware());

        });



// Catch-all route to serve a 404 Not Found page if none of the routes match
// NOTE: make sure this route is defined last
        $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
            $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
            return $handler($req, $res);
        });



        $app->run();
    }


}