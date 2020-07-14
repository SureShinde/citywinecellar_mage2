<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Sales\Order;
interface InvoiceManagementInterface extends \Magento\Sales\Api\InvoiceManagementInterface
{
    /**
     * @param \Magento\Sales\Model\Order $order
     * @param string[] $qtys|null
     * @return \Magento\Sales\Model\Order\Invoice
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function preparePosInvoice(\Magento\Sales\Model\Order $order, array $qtys = null);
}
