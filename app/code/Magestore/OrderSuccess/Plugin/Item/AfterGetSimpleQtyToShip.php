<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Plugin\Item;

use Magestore\DropshipSuccess\Api\Data\DropshipShipmentInterface;

/**
 * Class AfterGetSimpleQtyToShip
 * @package Magestore\OrderSuccess\Plugin\Item
 */
class AfterGetSimpleQtyToShip
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    public function __construct
    (
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\RequestInterface $request
    )
    {
        $this->coreRegistry = $coreRegistry;
        $this->request = $request;
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $item
     * @return mixed
     */
    public function afterGetSimpleQtyToShip(\Magento\Sales\Model\Order\Item $item)
    {
//        if (
//            ($this->request->getModuleName() == 'ordersuccess' && $this->request->getActionName() == 'getItemsGrid') ||
//            ($this->request->getModuleName() == 'sales' &&
//                ($this->request->getActionName() == 'view') || $this->request->getActionName() == 'new')
//        ) {
//            $qty = $item->getQtyOrdered() - $item->getQtyShipped() - $item->getQtyRefunded()
//                - $item->getQtyCanceled() - $item->getQtyBackordered() - $item->getQtyPrepareship();
//
//            return max($qty, 0);
//        }

        $qty = $item->getQtyOrdered() - $item->getQtyShipped() - $item->getQtyRefunded()
            - $item->getQtyCanceled() - $item->getQtyBackordered();
        if ($this->needCalculateQtyPrepareShip()) {
            $qty -= $item->getQtyPrepareship();
        }
        return max($qty, 0);
    }

    /**
     * Retrieve item qty available for cancel
     *
     * @return float|integer
     */
    public function afterGetQtyToCancel(\Magento\Sales\Model\Order\Item $item)
    {
        $qtyToShip = $item->getQtyToShip();
        $qtyToShip += $item->getQtyBackordered();
        if ($this->needCalculateQtyPrepareShip()) {
            $qtyToShip += $item->getQtyPrepareship();
        }
        $qtyToCancel = min($item->getQtyToInvoice(), $qtyToShip);
        return max($qtyToCancel, 0);
    }

    public function needCalculateQtyPrepareShip()
    {
        $moduleName = $this->request->getModuleName();
        if (!$this->coreRegistry->registry(DropshipShipmentInterface::CREATE_SHIPMENT_BY_DROPSHIP) &&
            ($moduleName != 'fulfilsuccess' && $moduleName != 'dropship')) {
            return true;
        }
        return false;
    }
}
