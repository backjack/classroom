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
class GetAdminInfo extends AbstractViewHelper  {

    public function __invoke($id) {

        // TODO Auto-generated LoggedIn::__invoke

        $sm = $this->getServicelocator();


        $accountsTable = new AccountsTable($sm);
        if($accountsTable->recordExists($id)){
            $row = $accountsTable->getRecord($id);
            return $row;
        }
        else{
            return null;
        }



    }




}
