<?php

/**
 * Application\View\Helper
 *
 * @author
 * @version
 */
namespace Application\View\Helper;

use Application\Model\LessonTable;
use Application\Model\SessionCategoryTable;
use Zend\View\Helper\AbstractHelper;

use Zend\ServiceManager\ServiceLocatorInterface;
/**
 * View Helper
 */
class GetCategories extends AbstractViewHelper  {

    public function __invoke() {

        // TODO Auto-generated GetArticle::__invoke
        $sm = $this->getServicelocator();

        $table = new SessionCategoryTable($sm);
        $rowset = $table->getLimitedRecords(100);
        $rowset->buffer();

        return $rowset;
    }


}
