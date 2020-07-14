<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Invoice\Item;
use Magento\TestFramework\Event\Magento;
use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface;
use Magestore\PurchaseOrderSuccess\Api\Data\InvoiceInterface;
use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface;
use Magestore\PurchaseOrderSuccess\Api\InvoiceItemRepositoryInterface;
use Magestore\PurchaseOrderSuccess\Api\PurchaseOrderItemRepositoryInterface;
use Magestore\PurchaseOrderSuccess\Api\Data\InvoiceItemInterface;

/**
 * Class ItemService
 * @package Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Invoice\Item
 */
class ItemService
{
    /**
     * @var InvoiceItemRepositoryInterface
     */
    protected $invoiceItemRepository;

    /**
     * @var PurchaseOrderItemRepositoryInterface
     */
    protected $purchaseOrderItemRepository;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Invoice\ItemFactory
     */
    protected $invoiceItemFactory;
    
    
    protected $taxType;
    
    public function __construct(
        InvoiceItemRepositoryInterface $invoiceItemRepository,
        PurchaseOrderItemRepositoryInterface $purchaseOrderItemRepository,
        \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Invoice\ItemFactory $invoiceItemFactory,
        \Magestore\PurchaseOrderSuccess\Service\Config\TaxAndShipping $taxShippingService
    ){
        $this->invoiceItemRepository = $invoiceItemRepository;
        $this->purchaseOrderItemRepository = $purchaseOrderItemRepository;
        $this->invoiceItemFactory = $invoiceItemFactory;
        $this->taxShippingService = $taxShippingService;
    }
    
    public function getTaxType(){
        if(empty($this->taxType))
            $this->taxType = $this->taxShippingService->getTaxType();
        return $this->taxType;
    }

    /**
     * Set qty billed for invoice item data
     * 
     * @param PurchaseOrderItemInterface $purchaseItem
     * @param array $invoiceItemData
     * @return array
     */
    public function setQtyBilled(PurchaseOrderItemInterface $purchaseItem, $invoiceItemData = []){
        $qty = $purchaseItem->getQtyOrderred() - $purchaseItem->getQtyBilled();
        if(!isset($invoiceItemData['qty_billed']) || $invoiceItemData['qty_billed'] > $qty)
            $invoiceItemData['qty_billed'] = $qty;
        return $invoiceItemData;
    }

    /**
     * @param $invoiceId
     * @param PurchaseOrderItemInterface $purchaseItem
     * @param array $invoiceItemData
     * @return InvoiceItemInterface
     */
    public function prepareInvoiceItem($invoiceId, PurchaseOrderItemInterface $purchaseItem, $invoiceItemData = []){
        $invoiceItemData = $this->setQtyBilled($purchaseItem, $invoiceItemData);
        return $this->invoiceItemFactory->create()
            ->setPurchaseOrderInvoiceId($invoiceId)
            ->setPurchaseOrderItemId($purchaseItem->getPurchaseOrderItemId())
            ->setQtyBilled($invoiceItemData['qty_billed'])
            ->setUnitPrice($invoiceItemData['unit_price'])
            ->setTax($invoiceItemData['tax'])
            ->setDiscount($invoiceItemData['discount']);
    }

    /**
     * Process invoice data
     * 
     * @param PurchaseOrderInterface $purchaseOrder
     * @param InvoiceInterface $invoice
     * @param InvoiceItemInterface $invoiceItem
     */
    public function processInvoiceData(
        PurchaseOrderInterface $purchaseOrder, InvoiceInterface $invoice, InvoiceItemInterface $invoiceItem
    ){
        $billedQty = $invoiceItem->getQtyBilled();
        $discountPercent = $invoiceItem->getDiscount();
        $taxPercent = $invoiceItem->getTax();
        $subtotal = $invoiceItem->getUnitPrice()*$billedQty;
        $discount = $subtotal*($discountPercent?$discountPercent:0)/100;
        if($this->getTaxType() == 0){
            $tax = $subtotal*($taxPercent?$taxPercent:0)/100;
        }else{
            $tax = ($subtotal-$discount)*($taxPercent?$taxPercent:0)/100;
        }
        $invoice->setTotalQtyBilled($invoice->getTotalQtyBilled() + $billedQty);
        $invoice->setSubtotal($invoice->getSubtotal() + $subtotal);
        $invoice->setTotalDiscount($invoice->getTotalDiscount() + $discount);
        $invoice->setTotalTax($invoice->getTotalTax() + $tax);
    }
    
    /**
     * Create an invoice item
     * 
     * @param PurchaseOrderInterface $purchaseOrder
     * @param InvoiceInterface $invoice
     * @param PurchaseOrderItemInterface $item
     * @param array $invoiceItemData
     * @return bool
     */
    public function createInvoiceItem(
        PurchaseOrderInterface $purchaseOrder, InvoiceInterface $invoice, PurchaseOrderItemInterface $purchaseItem, $invoiceItemData = []
    ){
        $invoiceItemData = $this->setQtyBilled($purchaseItem, $invoiceItemData);
        if($invoiceItemData['qty_billed'] == 0)
            return true;
        $invoiceItem = $this->prepareInvoiceItem($invoice->getPurchaseOrderInvoiceId(), $purchaseItem, $invoiceItemData);
        try{
            $this->invoiceItemRepository->save($invoiceItem);
            $purchaseItem->setQtyBilled($purchaseItem->getQtyBilled() + $invoiceItem->getQtyBilled());
            $this->purchaseOrderItemRepository->save($purchaseItem);
            $this->processInvoiceData($purchaseOrder, $invoice, $invoiceItem);
        }catch (\Exception $e){
            return false;
        }
        return true;
    }
}