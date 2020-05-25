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
class HasGroupPermission extends AbstractViewHelper {
    public function __invoke($groupId) {

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
        //get all permissions for this group
        $permissions = $permissionTable->getGroupPermissions($groupId);
        $status = false;
        foreach($permissions as $permissionRow){
            if($permissionTable->hasPermission($permissionRow->path)){
                $status=true;
            }
        }
        return $status;

    }


}
