<?php

namespace Magestore\Webpos\Model\Checkout\Data;

class Rule extends \Magento\SalesRule\Model\Data\Rule implements \Magestore\Webpos\Api\Data\Checkout\RuleInterface {
    /**
     * @inheritdoc
     */
    public function getValidItemIds(){
        return $this->_get(self::VALID_ITEM_IDS);
    }
    /**
     * @inheritdoc
     */
    public function setValidItemIds($validItemIds){
        return $this->setData(self::VALID_ITEM_IDS, $validItemIds);
    }
}