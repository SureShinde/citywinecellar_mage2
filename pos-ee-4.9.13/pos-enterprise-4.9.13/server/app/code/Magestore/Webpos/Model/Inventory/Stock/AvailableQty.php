<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Inventory\Stock;


/**
 * Class StockItemRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AvailableQty extends \Magento\Framework\DataObject
    implements \Magestore\Webpos\Api\Data\Inventory\AvailableQtyInterface
{
    /**
     * @inheritdoc
     */
    public function getAvailableQty() {
        return $this->getData(self::AVAILABLE_QTY);
    }

    /**
     * @inheritdoc
     */
    public function setAvailableQty($availableQty) {
        return $this->setData(self::AVAILABLE_QTY, $availableQty);
    }
}
