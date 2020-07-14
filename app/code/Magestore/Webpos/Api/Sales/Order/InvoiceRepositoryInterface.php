<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Sales\Order;

/**
 * Interface InvoiceRepositoryInterface
 * @package Magestore\Webpos\Api\Sales\Order
 */
interface InvoiceRepositoryInterface
{
    /**
     * Create new invoice by order id
     *
     * @param int $orderId
     * @return int
     * @throws \Exception
     */
    public function createInvoiceByOrderId($orderId);
}
