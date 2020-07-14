<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Model\ResourceModel\IncrementId\IncrementId;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\AdjustStock\Model\IncrementId\IncrementId', 'Magestore\AdjustStock\Model\ResourceModel\IncrementId\IncrementId');
    }
}
