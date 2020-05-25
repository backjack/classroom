<?php

/**
 * Application\View\Helper
 *
 * @author
 * @version
 */
namespace Application\View\Helper;

use Application\Model\AccountsTable;
use Application\Model\PermissionTable;
use Intermatics\UtilityFunctions;
use Zend\View\Helper\AbstractHelper;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * View Helper
 */
class HasPermission extends AbstractViewHelper{

    public function __invoke($permission) {

        // TODO Auto-generated LoggedIn::__invoke

        $sm = $this->getServicelocator();
        $authService = $sm->get('AdminAuthService');

        $identity = $authService->getIdentity();
        $email = $identity['email'];

        $accountsTable = new AccountsTable($sm);
        $row = $accountsTable->getAccountWithEmail($email);

        //get role
        $roleId = $row->role_id;

        $permissionTable = new PermissionTable($sm);
        $permissionRow = $permissionTable->getRecordForPermission($permission);

        return $permissionTable->hasPermission($permissionRow->path);

    }



}
