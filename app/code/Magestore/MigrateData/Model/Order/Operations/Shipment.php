<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\MigrateData\Model\Order\Operations;

/**
 * Class Creditmemo
 * @package Magestore\Webpos\Model\Sales\Order
 */
class Shipment extends \Magento\Framework\DataObject
{
    public function createShipment($order_id, $shipped_item, $date)
    {
        $order = $this->getOrderModel($order_id);
        try {
            if ($order->canShip()) {
    
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                /** @var \Magento\Sales\Model\Service\ShipmentService $shipment */
//                $shipment = $objectManager->create(\Magento\Sales\Model\Service\ShipmentService::class)
////                    Mage::getModel('sales/order_invoice_api')
//                                         ->prepareShipment($order, $shipped_item);
                /** @var \Magento\Sales\Model\Order\Shipment $orderShipment */
                $orderShipment = $objectManager->create(\Magento\Sales\Model\Convert\Order::class)->toShipment($order);
//                foreach ($shipped_item as $shipmentItem) {
//                    var_dump($shipmentItem);die('111');
//                    $orderShipment->addItem($shipmentItem);
//                }
                foreach ($order->getAllItems() as $orderItem) {
//                    var_dump($orderItem->getId());
                    // Check virtual item and item Quantity
                    if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                        continue;
                    }
                    if ($shipped_item[$orderItem->getId()]) {
                        $qty = $shipped_item[$orderItem->getId()];
                        $shipmentItem = $objectManager->create(\Magento\Sales\Model\Convert\Order::class)->itemToShipmentItem($orderItem)->setQty($qty);
    
                        $orderShipment->addItem($shipmentItem);
                    }
                }
//                var_dump($shipped_item);
//                die('xxxx');
                $orderShipment->setCreatedAt($date);
                $orderShipment->setUpdatedAt($date);
                $orderShipment->register();
                $orderShipment->getOrder()->setIsInProcess(true);
                try {
        
                    // Save created Order Shipment
                    $orderShipment->save();
                    $orderShipment->getOrder()->save();
                    
                    $orderShipment->save();
                } catch (\Exception $e) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __($e->getMessage())
                    );
                }
//
//                $transactionSave = $objectManager->create(\Magento\Framework\DB\Transaction::class)->addObject(
//                    $shipment
//                )->addObject(
//                    $shipment->getOrder()
//                );
//                $transactionSave->save();
//                $shipId = Mage::getModel('sales/order_shipment_api')
//                              ->create($order_id, $shipped_item, null, 0, 0);
//
//                if ($shipId) {
//                    Mage::getSingleton("sales/order_shipment")->loadByIncrementId($shipId)
//                        ->setCreatedAt($date)
//                        ->setUpdatedAt($date)
//                        ->save()
//                        ->unsetData();
//                    $this->updateShipmentQTY($shipped_item);
//                }
            }
        } catch (Exception $e) {
//            Mage::helper('exporter')->logException($e, $order->getIncrementId(), 'shipment');
//            Mage::helper('exporter')->footer();
            return true;
        }
        
        $order->unsetData();
        return $orderShipment;
    }
    
    public function updateShipmentQTY($shipped_item)
    {
        foreach ($shipped_item as $itemid => $itemqty) {
            $orderItem = Mage::getModel('sales/order_item')->load($itemid);
            $orderItem->setQtyShipped($itemqty)->save();
            $orderItem->unsetData();
        }
    }
    
    public function getOrderModel($last_order_increment_id)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create(\Magento\Sales\Model\Order::class)->loadByIncrementId($last_order_increment_id);
        return $order;
    }
}
