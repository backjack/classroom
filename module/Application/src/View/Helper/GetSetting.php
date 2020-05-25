<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 3/1/2018
 * Time: 8:28 PM
 */

namespace Application\View\Helper;
use Application\Model\SettingTable;

class GetSetting extends AbstractViewHelper {


    public function __invoke($setting,$default=null)
    {

        $setingTable = new SettingTable($this->getServiceManager());

        $result= $setingTable->getSetting($setting);

        if(!empty($result)){
            return $result;
        }
        elseif(!empty($default)){
            return $default;
        }
        else{
            return $result;
        }
    }


}