<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Model\ResourceModel\AdjustStock;

/**
 * Class Collection
 * @package Magestore\AdjustStock\Model\ResourceModel\AdjustStock
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init('Magestore\AdjustStock\Model\AdjustStock', 'Magestore\AdjustStock\Model\ResourceModel\AdjustStock');
    }
}
