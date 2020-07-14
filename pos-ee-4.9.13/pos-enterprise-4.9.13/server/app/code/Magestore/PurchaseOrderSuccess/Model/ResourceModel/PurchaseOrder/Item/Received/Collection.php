<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item\Received;

/**
 * Class Collection
 * @package Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item\Received
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'purchase_order_item_received_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Item\Received', 
            'Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item\Received');
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getFirstItem()
    {
        return $this->setPageSize(1)->setCurPage(1)->getFirstItem();
    }
}