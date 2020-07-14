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
class Invoice extends \Magento\Framework\DataObject
{
    public function createInvoice($order_id, $invoice_item, $date)
    {
        $order = $this->getOrderModel($order_id);
        try {
            if ($order->canInvoice()) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $invoice = $objectManager->create(\Magento\Sales\Model\Service\InvoiceService::class)
//                    Mage::getModel('sales/order_invoice_api')
                                 ->prepareInvoice($order, $invoice_item);
                $invoice->setCreatedAt($date);
                $invoice->setUpdatedAt($date);
                $invoice->register();
                $transactionSave = $objectManager->create(\Magento\Framework\DB\Transaction::class)->addObject(
                    $invoice
                )->addObject(
                    $invoice->getOrder()
                );
                $transactionSave->save();
//                if ($invoiceId) {
//                    Mage::getSingleton("sales/order_invoice")->loadByIncrementId($invoiceId)
//                        ->setCreatedAt($date)
//                        ->setUpdatedAt($date)
//                        ->save()
//                        ->unsetData();
//                    $this->updateInvoiceQTY($invoice_item);
//                }
            }
        } catch (Exception $e) {
//            Mage::helper('exporter')->logException($e, $order->getIncrementId(), 'invoice');
//            Mage::helper('exporter')->footer();
            return true;
        }
        
        $order->unsetData();
        return $invoice->getId();
    }
    
    public function updateInvoiceQTY($invoice_item)
    {
        foreach ($invoice_item as $itemid => $itemqty) {
            $orderItem = Mage::getModel('sales/order_item')->load($itemid);
            $orderItem->setQtyInvoiced($itemqty)->save();
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
