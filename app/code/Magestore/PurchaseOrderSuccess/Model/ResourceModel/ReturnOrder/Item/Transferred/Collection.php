<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\Item\Transferred;

/**
 * Class Collection
 * @package Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\Item\Transferred
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'return_item_transferred_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\PurchaseOrderSuccess\Model\ReturnOrder\Item\Transferred',
            'Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\Item\Transferred');
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getFirstItem()
    {
        return $this->setPageSize(1)->setCurPage(1)->getFirstItem();
    }
}