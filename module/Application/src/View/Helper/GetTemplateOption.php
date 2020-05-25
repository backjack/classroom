<?php

/**
 * Application\View\Helper
 *
 * @author
 * @version
 */
namespace Application\View\Helper;

use Application\Model\SettingTable;
use Application\Model\TemplateOptionTable;
use Application\Model\TemplateTable;
use Zend\View\Helper\AbstractHelper;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * View Helper
 */
class GetTemplateOption extends AbstractViewHelper{
  
    public function __invoke($setting,$default=null) {
        // TODO Auto-generated CompanyName::__invoke
        $sm = $this->getServicelocator();
        $templateOptionTable = new TemplateOptionTable($sm);

        $result= $templateOptionTable->getOption(TID,$setting);
        if(!empty($result)){
            return $result;
        }
        elseif(!empty($default)){
            return $default;
        }
        else{
            return false;
        }
    }


}
