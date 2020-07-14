<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Invoice\Refund\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magestore\PurchaseOrderSuccess\Api\InvoiceRepositoryInterface;
use Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Invoice\CollectionFactory;

/**
 * Class Refund
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Invoice\Refund\Form
 */
class Refund extends AbstractDataProvider
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
        CollectionFactory $collectionFactory,
        InvoiceRepositoryInterface $invoiceRepository,
        \Magento\Framework\App\RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
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
        
        return $this->data;
    }
}