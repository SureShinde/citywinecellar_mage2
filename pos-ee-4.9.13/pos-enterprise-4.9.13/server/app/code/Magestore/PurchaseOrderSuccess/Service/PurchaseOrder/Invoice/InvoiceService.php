<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Invoice;

use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface;
use Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface;
use Magestore\PurchaseOrderSuccess\Api\InvoiceRepositoryInterface;
use Magestore\PurchaseOrderSuccess\Api\Data\InvoiceInterface;
use Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item\Collection as PurchaseItemCollection;
use Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Invoice\Item\ItemService;

/**
 * Class InvoiceService
 * @package Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item
 */
class InvoiceService 
{
    /**
     * @var PurchaseOrderRepositoryInterface
     */
    protected $purchaseOrderRepository;

    /**
     * @var InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * @var InvoiceInterface
     */
    protected $invoiceInterface;

    /**
     * @var ItemService
     */
    protected $invoiceItemService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\InvoiceFactory
     */
    protected $invoiceFactory;
    
    public function __construct(
        PurchaseOrderRepositoryInterface $purchaseOrderRepository,
        InvoiceRepositoryInterface $invoiceRepository,
        InvoiceInterface $invoiceInterface,
        Item\ItemService $invoiceItemService,
        \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\InvoiceFactory $invoiceFactory
    ){
        $this->purchaseOrderRepository = $purchaseOrderRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->invoiceInterface = $invoiceInterface;
        $this->invoiceItemService = $invoiceItemService;
        $this->invoiceFactory = $invoiceFactory;
    }
    
    /**
     * Process create invoice data
     * 
     * @param array $params
     * @return array
     */
    public function processInvoiceParam($params = []){
        $result = [];
        foreach ($params as $item){
            if(!isset($item['qty_billed']) || !isset($item['unit_price']))
                continue;
            if($item['qty_billed']<=0 || $item['unit_price']<=0)
                continue;
            $result[$item['id']] = [
                'product_id' => $item['id'],
                'qty_billed' => $item['qty_billed'],
                'unit_price' => $item['unit_price'],
                'tax' => $item['tax'],
                'discount' => $item['discount']
            ];
        }
        return $result;
    }

    /**
     * Prepare invoice data
     * 
     * @param PurchaseOrderInterface $purchaseOrder
     * @param $invoiceTime
     * @param $createdBy
     * @return \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Invoice
     */
    public function prepareInvoiceData(PurchaseOrderInterface $purchaseOrder, $invoiceTime, $createdBy){
        return $this->invoiceFactory->create()
            ->setPurchaseOrderId($purchaseOrder->getPurchaseOrderId())
            ->setBilledAt($invoiceTime);
    }

    /**
     * Create an invoice and add item item
     * 
     * @param PurchaseOrderInterface $purchaseOrder
     * @param PurchaseItemCollection $purchaseItems
     * @param array $invoiceData
     * @param string|null $invoiceTime
     * @param string|null $createdBy
     * @return $this
     */
    public function createInvoice(
        PurchaseOrderInterface $purchaseOrder, PurchaseItemCollection $purchaseItems, $invoiceData = [], $invoiceTime = null, $createdBy = null
    ){
        $invoice = $this->prepareInvoiceData($purchaseOrder, $invoiceTime, $createdBy);
        $this->invoiceRepository->save($invoice);
        foreach ($purchaseItems as $item) {
            $productId = $item->getProductId();
            if(!in_array($productId, array_keys($invoiceData)))
                continue;
            $result = $this->invoiceItemService->createInvoiceItem(
                $purchaseOrder, $invoice, $item, $invoiceData[$productId]
            );
            if(!$result)
                $productSkus[] = $item->getProductSku();
        }
        $this->processInvoiceAndPurchaseData($purchaseOrder, $invoice);
        $this->invoiceRepository->save($invoice);
        $this->purchaseOrderRepository->save($purchaseOrder);
        return $this;
    }

    /**
     * Process Invoice data and purchase order data
     * 
     * @param PurchaseOrderInterface $purchaseOrder
     * @param InvoiceInterface $invoice
     */
    public function processInvoiceAndPurchaseData(PurchaseOrderInterface $purchaseOrder, InvoiceInterface $invoice){
        $invoice->setGrandTotalExclTax($invoice->getSubtotal()-$invoice->getTotalDiscount());
        $invoice->setGrandTotalInclTax($invoice->getGrandTotalExclTax()+$invoice->getTotalTax());
        $invoice->setTotalDue($invoice->getGrandTotalInclTax());
        $purchaseOrder->setTotalQtyBilled($purchaseOrder->getTotalQtyBilled()+$invoice->getTotalQtyBilled());
        $purchaseOrder->setTotalBilled($purchaseOrder->getTotalBilled()+$invoice->getGrandTotalInclTax());
    }
}