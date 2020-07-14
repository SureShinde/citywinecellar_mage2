<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Service;

use Magestore\OrderSuccess\Api\Data\ShippingChanelInterface;

/**
 * Class ShipService
 * @package Magestore\OrderSuccess\Service
 */
class ShipService
{
    
    /**
     * @var \Magestore\OrderSuccess\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magestore\OrderSuccess\Api\OrderItemRepositoryInterface
     */
    protected $orderItemRepository;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * ShipService constructor.
     * @param \Magestore\OrderSuccess\Api\OrderRepositoryInterface $orderRepository
     * @param \Magestore\OrderSuccess\Api\OrderItemRepositoryInterface $orderItemRepository
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        \Magestore\OrderSuccess\Api\OrderRepositoryInterface $orderRepository,
        \Magestore\OrderSuccess\Api\OrderItemRepositoryInterface $orderItemRepository,
        \Magento\Framework\Event\ManagerInterface $eventManager
        )
    {
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->eventManager = $eventManager;
    }

    /**
     * process shipment
     *
     * @param int $orderId
     */
    public function processShipment($data)
    {
        $packages = $data['packages'];
        foreach($packages as $package){
            $params = $package['params'];
            $chanels = $params['container'];
            if($chanels == ShippingChanelInterface::BACKORDER){
                if(isset($package['items'])){
                    $this->moveItemToBackOrder($package['items']);
                }
            }
        }
        unset($data['key']);
        unset($data['isAjax']);
        unset($data['form_key']);
        $shipData = new \Magento\Framework\DataObject($data);
        $this->eventManager->dispatch('ordersuccess_process_shipment', ['ship_data' => $shipData]);
    }

    /**
     * Move item to back order
     *
     * @param int $orderId
     */
    public function moveItemToBackOrder($items)
    {
        foreach ($items as $item){
            $itemId = $item['order_item_id'];
            $qty = $item['qty'];
            $this->orderItemRepository->moveItemToBackOrder($itemId, $qty);
        }
    }
    
    /**
     * Remove item from back order
     *
     * @param int $orderId
     */
    public function removeFromBackOrder($orderId)
    {
        $order = $this->orderRepository->get($orderId);
        $items = $order->getAllItems();
        foreach ($items as $item){
            $this->orderItemRepository->removeFromBackOrder($item);
        }
    }

    /**
     * Remove order items from back order
     *
     * @param int $orderId
     */
    public function removeFromBackOrders($orderIds)
    {
        foreach($orderIds as $orderId) {
            $order = $this->orderRepository->get($orderId);
            $items = $order->getAllItems();
            foreach ($items as $item) {
                $this->orderItemRepository->removeFromBackOrder($item);
            }
        }
    }

}