<?php

/**
 * Application\View\Helper
 *
 * @author
 * @version
 */
namespace Application\View\Helper;

use Application\Model\AccountsTable;
use Zend\View\Helper\AbstractHelper;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * View Helper
 */
class GetAdmin extends AbstractViewHelper    {

    public function __invoke() {

        // TODO Auto-generated LoggedIn::__invoke

        $sm = $this->getServicelocator();
        $authService = $sm->get('AdminAuthService');

        $identity = $authService->getIdentity();
        $email = $identity['email'];

        $accountsTable = new AccountsTable($sm);
        $row = $accountsTable->getAccountWithEmail($email);

        return $row;
    }


}
