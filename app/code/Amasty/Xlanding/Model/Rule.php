<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Model;

class Rule extends \Magento\CatalogRule\Model\Rule
{
    protected $serializer;

    protected function _construct()
    {
        $amastySerializer = $this->getData('amastySerializer');
        if ($amastySerializer) {
            $this->serializer = $amastySerializer;
        }
        parent::_construct();
        $this->_init('Amasty\Xlanding\Model\ResourceModel\Page');
        $this->setIdFieldName('page_id');
    }
}
