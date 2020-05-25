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
class HasPermissionPath extends AbstractViewHelper    {

    public function __invoke($permission) {

        // TODO Auto-generated LoggedIn::__invoke

        $sm = $this->getServicelocator();



        $permissionTable = new PermissionTable($sm);

        return $permissionTable->hasPermission($permission);

    }



}
