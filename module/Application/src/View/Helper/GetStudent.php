<?php

/**
 * Application\View\Helper
 *
 * @author
 * @version
 */
namespace Application\View\Helper;

use Application\Model\AccountsTable;
use Application\Model\StudentTable;
use Zend\View\Helper\AbstractHelper;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * View Helper
 */
class GetStudent extends AbstractViewHelper {

    public function __invoke() {

        // TODO Auto-generated LoggedIn::__invoke

        $sm = $this->getServiceLocator();
        $authService = $sm->get('AdminAuthService');

        $identity = $authService->getIdentity();
        $email = $identity['email'];

        $accountsTable = new StudentTable($sm);
        $row = $accountsTable->getStudentWithEmail($email);

        return $row;
    }




}
