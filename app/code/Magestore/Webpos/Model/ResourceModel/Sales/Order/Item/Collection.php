<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\ResourceModel\Sales\Order\Item;


class Collection extends \Magento\Sales\Model\ResourceModel\Order\Item\Collection
{
    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magestore\Webpos\Model\Checkout\Order\Item::class, \Magestore\Webpos\Model\ResourceModel\Sales\Order\Item::class);
    }
}
