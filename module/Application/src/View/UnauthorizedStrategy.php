<?php
namespace Application\View;
use BjyAuthorize\Service\Authorize;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\Mvc\Application;
use BjyAuthorize\Exception\UnAuthorizedException;
use BjyAuthorize\Guard\Controller;
use BjyAuthorize\Guard\Route;
use Zend\Session\SessionManager;
class UnauthorizedStrategy implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();
    public function attach(EventManagerInterface $events, $priority=1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onDispatchError'), -5000);
    }
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }
    public function onDispatchError(MvcEvent $e)
    {



        // Do nothing if the result is a response object
        $result = $e->getResult();

        if ($result instanceof Response) {
            return;
        }
        $router = $e->getRouter();
        $match  = $e->getRouteMatch();
        // get url to the zfcuser/login route
     //   $options['name'] = 'zfcuser/login';

        $url=$_SERVER['REQUEST_URI'];
        if (preg_match('#admin#', $url)) {

            $route = 'admin/signin';
            $session = new Container('admin_login');
        }
        else{
            $route = 'application/signin';
            $session = new Container('student_login');
        }



        $options['name'] = $route;

        if($e->getError() == Route::ERROR)
        {
            //clear session

            $url = $router->assemble(array(), $options);
            // Work out where were we trying to get to
            //$options['name'] = $match->getMatchedRouteName();
            //$redirect = $router->assemble($match->getParams(), $options);

            //testing
            $redirect = selfURL();
            // set up response to redirect to login page
            $response = $e->getResponse();
            if (!$response) {
                $response = new HttpResponse();
                $e->setResponse($response);
            }

            $session->url = $redirect;
            $response->getHeaders()->addHeaderLine('Location', $url );
            $response->setStatusCode(302);
        }

    }
}