<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel\Sales;

class Order extends \Magento\Sales\Model\ResourceModel\Order
{
    /**
     * Get all order currency
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAllOrderCurrency()
    {
        $connection = $this->getConnection();
        $currencySelect = $connection->select()->from($this->getMainTable(), [])
            ->columns('order_currency_code')
            ->group('order_currency_code');
        return $connection->fetchCol($currencySelect);
    }
}
