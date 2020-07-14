<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Model\Repository;

use Magestore\DropshipSuccess\Api\OrderItemRepositoryInterface;
use Magestore\DropshipSuccess\Api\Data\OrderItemInterface;

/**
 * Class OrderItemRepository
 * @package Magestore\DropshipSuccess\Model\Repository
 */
class OrderItemRepository extends \Magento\Sales\Model\Order\ItemRepository
                      implements OrderItemRepositoryInterface
{

    /**
     * update dropshi qty
     *
     * @param array $id
     * @param \Magestore\DropshipSuccess\Api\decimal $qty
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function updateDropshipQty($id, $qty)
    {
        $item = $this->get($id);
        $oldQty = $item->getData(OrderItemInterface::QTY_PREPARESHIP);
        $qtyPrepareShip = max(0, $oldQty + $qty);
        $item->setData(OrderItemInterface::QTY_PREPARESHIP, $qtyPrepareShip);
        $this->save($item);
    }

}

