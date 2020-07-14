<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\Received;

use Magestore\PurchaseOrderSuccess\Api\PurchaseOrderItemRepositoryInterface;
use Magestore\PurchaseOrderSuccess\Api\PurchaseOrderItemReceivedRepositoryInterface;
use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface;
use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Item\ReceivedFactory;

/**
 * Class ReceivedService
 * @package Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\Received
 */
class ReceivedService
{
    /**
     * @var PurchaseOrderItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderItemReceivedRepositoryInterface
     */
    protected $receivedRepository;

    /**
     * @var ReceivedFactory
     */
    protected $receivedFactory;


    /**
     * ReceivedService constructor.
     * @param PurchaseOrderItemRepositoryInterface $itemRepository
     * @param PurchaseOrderItemReceivedRepositoryInterface $receivedRepository
     * @param ReceivedFactory $receivedFactory
     */
    public function __construct(
        PurchaseOrderItemRepositoryInterface $itemRepository,
        PurchaseOrderItemReceivedRepositoryInterface $receivedRepository,
        ReceivedFactory $receivedFactory
    )
    {
        $this->itemRepository = $itemRepository;
        $this->receivedRepository = $receivedRepository;
        $this->receivedFactory = $receivedFactory;
    }

    /**
     * @param array $params
     * @return array
     */
    public function processReceivedData($params = [])
    {
        $result = [];
        foreach ($params as $item) {
            if(isset($item['received_qty'])) {
                $result[$item['id']] = $item['received_qty'];
            }
        }
        return $result;
    }

    /**
     * @param PurchaseOrderItemInterface $purchaseItem
     * @param float|null $receivedQty
     * @return float
     */
    public function getQtyReceived(PurchaseOrderItemInterface $purchaseItem, $receivedQty = null)
    {
        $qty = $purchaseItem->getQtyOrderred() - $purchaseItem->getQtyReceived();
        if (!$receivedQty || $receivedQty > $qty)
            $receivedQty = $qty;
        return $receivedQty;
    }

    /**
     * Prepare received item
     *
     * @param PurchaseOrderItemInterface $purchaseItem
     * @param int|null $receivedQty
     * @param string|null $receivedTime
     * @param string|null $createdBy
     * @return mixed
     */
    public function prepareItemReceived(
        PurchaseOrderItemInterface $purchaseItem, $receivedQty = null, $receivedTime = null, $createdBy = null
    )
    {
        $receivedQty = $this->getQtyReceived($purchaseItem, $receivedQty);
        return $this->receivedFactory->create()
            ->setPurchaseOrderItemId($purchaseItem->getPurchaseOrderItemId())
            ->setQtyReceived($receivedQty)
            ->setReceivedAt($receivedTime)
            ->setCreatedBy($createdBy);
    }

    /**
     * Receive an purchase item by purchase item and qty
     *
     * @param PurchaseOrderInterface $purchaseOrder
     * @param PurchaseOrderItemInterface $purchaseItem
     * @param float|null $receivedQty
     * @param string|null $receivedTime
     * @param string|null $createdBy
     * @return bool
     */
    public function receiveItem(
        PurchaseOrderInterface $purchaseOrder, PurchaseOrderItemInterface $purchaseItem, $receivedQty = null,
        $receivedTime = null, $createdBy = null
    )
    {
        $receivedQty = $this->getQtyReceived($purchaseItem, $receivedQty);
        if ($receivedQty == 0)
            return true;
        $itemReceived = $this->prepareItemReceived($purchaseItem, $receivedQty, $receivedTime, $createdBy);
        try {
            $this->receivedRepository->save($itemReceived);
            $purchaseItem->setQtyReceived($purchaseItem->getQtyReceived() + $receivedQty);
            $this->itemRepository->save($purchaseItem);
            $purchaseOrder->setTotalQtyReceived($purchaseOrder->getTotalQtyReceived() + $receivedQty);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
}
