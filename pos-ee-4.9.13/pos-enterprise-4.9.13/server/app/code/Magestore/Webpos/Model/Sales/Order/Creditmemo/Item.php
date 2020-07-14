<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Sales\Order\Creditmemo;

/**
 * Class InvoiceService
 */
class Item extends \Magento\Sales\Model\Order\Creditmemo\Item
    implements \Magestore\Webpos\Api\Data\Sales\Order\Creditmemo\ItemInterface
{

    /**
     * Get back to stock
     *
     * @return boolean|null
     */
    public function getBackToStock()
    {
        return $this->getData(self::BACK_TO_STOCK);
    }

    /**
     * Set back to stock
     *
     * @param boolean|null $posLocationId
     * @return $this
     */
    public function setBackToStock($backToStock)
    {
        return $this->setData(self::BACK_TO_STOCK, $backToStock);
    }

    /**
     * @inheritdoc
     */
    public function getPosLocationId()
    {
        return $this->getData(self::POS_LOCATION_ID);
    }

    /**
     * @inheritdoc
     */
    public function setPosLocationId($posLocationId)
    {
        return $this->setData(self::POS_LOCATION_ID, $posLocationId);
    }
}