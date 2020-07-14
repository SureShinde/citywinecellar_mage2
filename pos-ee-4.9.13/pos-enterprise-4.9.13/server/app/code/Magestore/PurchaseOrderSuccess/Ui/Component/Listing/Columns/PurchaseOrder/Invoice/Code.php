<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\PurchaseOrderSuccess\Ui\Component\Listing\Columns\PurchaseOrder\Invoice;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magestore\PurchaseOrderSuccess\Api\Data\InvoiceInterface;

/**
 * Class Actions
 * @package Magestore\PurchaseOrderSuccess\Ui\Component\Listing\Columns\PurchaseOrder\Invoice
 */
class Code extends Column
{
    const CODE_LENGTH = 8;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item['invoice_real_id'] = $item['purchase_order_invoice_id'];                
                $formatId = pow(10, InvoiceInterface::CODE_LENGTH + 1) + $item['purchase_order_invoice_id'];
                $formatId = (string) $formatId;
                $formatId = substr($formatId, 0-InvoiceInterface::CODE_LENGTH);
                $item[$this->getData('name')] = $formatId;
            }
        }

        return $dataSource;
    }
}
