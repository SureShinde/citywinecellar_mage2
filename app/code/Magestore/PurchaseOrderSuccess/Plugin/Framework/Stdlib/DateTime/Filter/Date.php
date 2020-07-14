<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\PurchaseOrderSuccess\Plugin\Framework\Stdlib\DateTime\Filter;

use Magento\Framework\App\ObjectManager;

class Date extends \Magento\Framework\Stdlib\DateTime\Filter\Date
{
    public function aroundFilter(\Magento\Framework\Stdlib\DateTime\Filter\Date $subject, callable $proceed, $value)
    {
        $productMetadata = ObjectManager::getInstance()->create('Magento\Framework\App\ProductMetadataInterface');
        if (version_compare($productMetadata->getVersion(), '2.2.6', '<')) {
            try {
                $dateTime = $this->_localeDate->date($value, null, false);
                return $dateTime->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                throw new \Exception("Invalid input datetime format of value '$value'", $e->getCode(), $e);
            }
        } else {
            return $proceed($value);
        }
    }
}