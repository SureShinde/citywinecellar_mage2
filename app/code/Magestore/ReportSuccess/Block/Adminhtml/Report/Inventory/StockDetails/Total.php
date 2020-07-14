<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Block\Adminhtml\Report\Inventory\StockDetails;
/**
 * Class Total
 * @package Magestore\ReportSuccess\Block\Adminhtml\Report\Inventory\StockDetails
 */
class Total extends \Magestore\ReportSuccess\Block\Adminhtml\Report\Inventory\AbstractTotal {
    /**
     * @return int
     */
    public function isPurchaseOrderEnable(){
        return $this->objectManager->get('Magento\Framework\Module\Manager')->isEnabled('Magestore_PurchaseOrderSuccess')?:0;
    }
}