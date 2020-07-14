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
class Creditmemo extends \Magento\Framework\DataObject
{
    public function createCreditMemo($order_id, $credit_item, $creditDetail)
    {
        $order = $this->getOrderModel($order_id);
        try {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            /** @var \Magento\Sales\Model\Service\ShipmentService $shipment */
//                $shipment = $objectManager->create(\Magento\Sales\Model\Service\ShipmentService::class)
////                    Mage::getModel('sales/order_invoice_api')
//                                         ->prepareShipment($order, $shipped_item);
            /** @var \Magento\Sales\Model\Order\Creditmemo $orderCreditmemo */
            $orderCreditmemo = $objectManager->create(\Magento\Sales\Model\Convert\Order::class)->toCreditmemo($order);

            foreach ($order->getAllItems() as $orderItem) {
                if ($credit_item[$orderItem->getId()]) {
                    $qty = $credit_item[$orderItem->getId()];
                    $creditmemoItem = $objectManager->create(\Magento\Sales\Model\Convert\Order::class)
                                                    ->itemToCreditmemoItem($orderItem)
                                                    ->setQty($qty)
                                                    ->setPrice($orderItem->getPrice());
    
                    $orderCreditmemo->addItem($creditmemoItem);
                }
            }
            $orderCreditmemo->collectTotals();
//                var_dump($shipped_item);
//                die('xxxx');
//            $orderCreditmemo->setCreatedAt($date);
//            $orderCreditmemo->setUpdatedAt($date);
            $creditmemoService = $objectManager->create('Magento\Sales\Model\Service\CreditmemoService');
            $creditmemoService->refund($orderCreditmemo);
//            die('1');
//            $orderCreditmemo->getOrder()->setIsInProcess(true);
            try {
//                die('111');
                // Save created Order Shipment
//                $orderCreditmemo->save();
//                $orderCreditmemo->getOrder()->save();
//
//                $orderCreditmemo->save();
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __($e->getMessage())
                );
            }
        } catch (Exception $e) {
            Mage::helper('exporter')->logException($e, $order->getIncrementId(), 'creditmemo');
            Mage::helper('exporter')->footer();
            return true;
        }
        
        $order->unsetData();
        return $orderCreditmemo;
    }
    
    public function updateCreditQTY($credit_item)
    {
//        foreach ($credit_item as $itemid => $itemqty) {
//            $orderItem = Mage::getModel('sales/order_item')->load($itemid);
//            $orderItem->setQtyRefunded($itemqty)->save();
//            $orderItem->unsetData();
//        }
    }
    
    public function updateStatus($order_id, $refunded)
    {
        $order = $this->getOrderModel($order_id);
        
        //set creditmemo data
        $order->setSubtotalRefunded($refunded['refunded_subtotal'])
              ->setBaseSubtotalRefunded($refunded['refunded_subtotal'])
              ->setTaxRefunded($refunded['refunded_tax_amount'])
              ->setBaseTaxRefunded($refunded['base_refunded_tax_amount'])
              ->setDiscountRefunded($refunded['refunded_discount_amount'])
              ->setBaseDiscountRefunded($refunded['base_refunded_discount_amount'])
              ->setShippingRefunded($refunded['refunded_shipping_amount'])
              ->setBaseShippingRefunded($refunded['base_refunded_shipping_amount'])
              ->setTotalRefunded($refunded['total_refunded'])
              ->setBaseTotalRefunded($refunded['base_total_refunded'])
              ->setTotalOfflineRefunded($refunded['total_refunded'])
              ->setBaseTotalOfflineRefunded($refunded['base_total_refunded'])
              ->setAdjustmentNegative($refunded['adjustment_positive'])
              ->setBaseAdjustmentNegative($refunded['adjustment_positive'])
              ->setAdjustmentPositive($refunded['adjustment_negative'])
              ->setBaseAdjustmentPositive($refunded['adjustment_negative'])
              ->save();
        $order->unsetData();
    }
    
    public function getOrderModel($last_order_increment_id)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create(\Magento\Sales\Model\Order::class)->loadByIncrementId($last_order_increment_id);
        
        return $order;
    }
}
