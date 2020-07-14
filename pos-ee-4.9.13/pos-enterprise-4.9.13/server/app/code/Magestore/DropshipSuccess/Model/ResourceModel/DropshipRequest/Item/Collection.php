<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\Item;

use Magento\Framework\DB\Select;

/**
 * Class DropshipRequest
 * @package Magestore\SupplierSuccess\Model\ResourceModel\Supplier
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'dropship_request_item_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\DropshipSuccess\Model\DropshipRequest\Item', 'Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\Item');
    }

    /**
     * Only get items can be shipped
     * 
     * @return $this
     */
    public function getCanShipItem(){
        return $this->addFieldToFilter(
            new \Zend_Db_Expr('main_table.qty_requested - main_table.qty_shipped - main_table.qty_canceled'),
            ['gt' => 0]
        );
    }
}