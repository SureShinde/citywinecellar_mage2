<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Invoice\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface;
use Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\CollectionFactory;

/**
 * Class Invoice
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Invoice\Form
 */
class Invoice extends AbstractDataProvider
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var PoolInterface
     */
    protected $pool;

    /**
     * @var PurchaseOrderRepositoryInterface
     */
    protected $purchaseOrderRepositoryInterface;

    /**
     * Warehouse constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param PoolInterface $pool
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        PoolInterface $pool,
        CollectionFactory $collectionFactory,
        PurchaseOrderRepositoryInterface $purchaseOrderRepositoryInterface,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        $this->registry = $registry;
        if($this->getCurrentInvoice()){
            $primaryFieldName = 'purchase_order_invoice_id';
            $requestFieldName = 'id';
        }
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->pool = $pool;
        $this->purchaseOrderRepositoryInterface = $purchaseOrderRepositoryInterface;
        $this->request = $request;
        $this->collection = $collectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $invoice = $this->getCurrentInvoice();
        if($invoice){
            $this->data[$invoice->getPurchaseOrderInvoiceId()] = $invoice->getData();
        }else{
            $purchaseOrder = $this->getCurrentPurchaseOrder();
            if($purchaseOrder && $purchaseOrder->getId()){
                $this->data[$purchaseOrder->getId()] = $purchaseOrder->getData();
            }
        }
        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $this->data = $modifier->modifyData($this->data);
        }
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }
        
        return $meta;
    }

    /**
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\InvoiceInterface
     */
    public function getCurrentInvoice(){
        return $this->registry->registry('current_purchase_order_invoice');
    }

    public function getCurrentPurchaseOrder(){
        $currentPurchaseOrder = $this->registry->registry('current_purchase_order');
        if(!$currentPurchaseOrder || !$currentPurchaseOrder->getPurchaseOrderId()){
            $purchaseId = $this->request->getParam('purchase_id');
            $currentPurchaseOrder = $this->purchaseOrderRepositoryInterface->get($purchaseId);
            $this->registry->register('current_purchase_order', $currentPurchaseOrder);
        }
        return $currentPurchaseOrder;
    }
}