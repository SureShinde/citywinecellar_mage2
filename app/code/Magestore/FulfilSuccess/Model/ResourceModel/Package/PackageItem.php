<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\ResourceModel\Package;

class PackageItem extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('os_fulfilsuccess_package_item', 'package_item_id');
    }

    /**
     * @param array $itemData
     * ['package_id', 'qty', 'price', 'name', 'weight', 'product_id', 'order_item_id']
     */
    public function addItems(array $itemData)
    {
        $connection = $this->getConnection();
        $table = $this->getTable('os_fulfilsuccess_package_item');
        $connection->insertOnDuplicate($table, $itemData);
    }
}