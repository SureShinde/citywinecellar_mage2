<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Model\Repository;

use Magestore\OrderSuccess\Api\OrderItemRepositoryInterface;
use Magento\Sales\Api\Data\OrderItemInterface;

/**
 * Class OrderItemRepository
 * @package Magestore\OrderSuccess\Model\Repository
 */
class OrderItemRepository extends \Magento\Sales\Model\Order\ItemRepository
                      implements OrderItemRepositoryInterface
{

    /**
     * move item to back order
     *
     * @param array $id
     * @param \Magestore\OrderSuccess\Api\decimal $qty
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function moveItemToBackOrder($id, $qty)
    {
        $item = $this->get($id, $qty);
        $oldBackQty = $item->getData(OrderItemInterface::QTY_BACKORDERED);
        $item->setData(OrderItemInterface::QTY_BACKORDERED, $oldBackQty + $qty);
        $this->save($item);
    }

    /**
     * remove item from back order
     *
     * @param $item
     */
    public function removeFromBackOrder($item)
    {
        $item->setData(OrderItemInterface::QTY_BACKORDERED, null);
        $this->save($item);
    }
}

