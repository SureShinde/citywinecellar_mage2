<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Sales\Order;

/**
 * Class InvoiceRepository
 *
 * @package Magestore\Webpos\Model\Sales\Order
 */
class InvoiceRepository implements \Magestore\Webpos\Api\Sales\Order\InvoiceRepositoryInterface
{
    /**
     * @var \Magento\Sales\Api\InvoiceOrderInterface
     */
    protected $invoiceOrder;

    /**
     * InvoiceRepository constructor.
     * @param \Magento\Sales\Api\InvoiceOrderInterface $invoiceOrder
     */
    public function __construct(
        \Magento\Sales\Api\InvoiceOrderInterface $invoiceOrder
    ) {
        $this->invoiceOrder = $invoiceOrder;
    }

    /**
     * @inheritDoc
     */
    public function createInvoiceByOrderId($orderId)
    {
        return $this->invoiceOrder->execute($orderId);
    }
}
