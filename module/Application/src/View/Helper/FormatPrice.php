<?php

/**
 * Application\View\Helper
 *
 * @author
 * @version
 */
namespace Application\View\Helper;

use Application\Model\CountryTable;
use Application\Model\SettingTable;
use Zend\View\Helper\AbstractHelper;

use Zend\ServiceManager\ServiceLocatorInterface;
/**
 * View Helper
 */
class FormatPrice extends AbstractViewHelper   {

    public function __invoke($in) {

        // TODO Auto-generated GetArticle::__invoke
    /*    $sm = $this->getServicelocator();

        $settingTable= new SettingTable($sm);
        $id = $settingTable->getSetting('country_id');
        if(empty($id)){
            $id = 223;
        }
        $countryTable = new CountryTable($sm);
        $row = $countryTable->getRecord($id);
        $symbol = $row->symbol_left;
        if(empty($symbol))
        {
            $symbol= $row->currency_code;
        }

        return $symbol.''.number_format($in);
    */
        return price($in);
    }


}
