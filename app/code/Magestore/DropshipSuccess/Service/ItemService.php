<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Service;

use Magestore\DropshipSuccess\Api\OrderItemRepositoryInterface;
use Magestore\DropshipSuccess\Model\OrderItemFactory;

/**
 * Class ItemService
 * @package Magestore\DropshipSuccess\Service
 */
class ItemService
{

    /**
     * @var OrderItemRepositoryInterface
     */
    protected $orderItemRepository;

    /**
     * @var OrderItemFactory
     */
    protected $orderItemFactory;

    /**
     * ItemService constructor.
     * @param OrderItemRepositoryInterface $orderItemRepositoryInterface
     * @param OrderItemFactory $orderItemFactory
     */
    public function __construct(
        OrderItemRepositoryInterface $orderItemRepositoryInterface,
        OrderItemFactory $orderItemFactory
    ) {
        $this->orderItemRepository = $orderItemRepositoryInterface;
        $this->orderItemFactory = $orderItemFactory;
    }

    /**
     * Update qty_prepareship in Sales Item
     *
     * @param \Magestore\DropshipSuccess\Api\Data\OrderItemInterface $item
     * @param float $changeQty
     */
    public function updatePrepareShipQty($item, $changeQty)
    {
        $qtyPrepareShip = max(0, $item->getQtyPrepareship() + $changeQty);
        $item->setQtyPrepareship($qtyPrepareShip);
        /* prepare OrderItem to save */
        $orderItem = $this->orderItemFactory->create();
        $orderItem->setData($item->getData());
        $this->orderItemRepository->save($orderItem);
    }

    /**
     * Update qty_prepareship by item_id
     *
     * @param $itemId
     * @param void
     */
    public function updateDropshipQty($itemId, $changeQty)
    {
        $this->orderItemRepository->updateDropshipQty($itemId, $changeQty);
    }
}