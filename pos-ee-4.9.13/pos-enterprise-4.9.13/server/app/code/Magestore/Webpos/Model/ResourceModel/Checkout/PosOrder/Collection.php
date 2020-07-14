<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel\Checkout\PosOrder;

/**
 * Class Collection
 *
 * @package Magestore\Webpos\Model\ResourceModel\Checkout\PosOrder
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Initialize collection resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Magestore\Webpos\Model\Checkout\PosOrder::class,
            \Magestore\Webpos\Model\ResourceModel\Checkout\PosOrder::class
        );
    }
}
