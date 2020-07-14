<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Invoice\Payment\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magestore\PurchaseOrderSuccess\Api\InvoiceRepositoryInterface;
use Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Invoice\CollectionFactory;

/**
 * Class Payment
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Invoice\Payment\Form
 */
class Payment extends AbstractDataProvider
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
    
    /**
     * @var InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * Warehouse constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        PoolInterface $pool,
        CollectionFactory $collectionFactory,
        InvoiceRepositoryInterface $invoiceRepository,
        \Magento\Framework\App\RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->pool = $pool;
        $this->request = $request;
        $this->invoiceRepository = $invoiceRepository;
        $this->collection = $collectionFactory->create();
    }

    public function getData()
    {
        $invoice = $this->invoiceRepository->get($this->request->getParam($this->getRequestFieldName()));
        
        if($invoice && $invoice->getPurchaseOrderInvoiceId()){
            $this->data[$invoice->getPurchaseOrderInvoiceId()] = $invoice->getData();
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
}