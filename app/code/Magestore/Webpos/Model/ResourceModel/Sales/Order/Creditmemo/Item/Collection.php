<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\ResourceModel\Sales\Order\Creditmemo\Item;

/**
 * Class Collection
 * @package Magestore\Webpos\Model\ResourceModel\Sales\Order\Creditmemo\Item
 */
class Collection
        extends \Magento\Sales\Model\ResourceModel\Order\Creditmemo\Item\Collection
{
    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Magestore\Webpos\Model\Sales\Order\Creditmemo\Item::class,
            \Magestore\Webpos\Model\ResourceModel\Sales\Order\Creditmemo\Item::class
        );
    }

}
