<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Model\ResourceModel;

/**
 * Class Supplier
 * @package Magestore\SupplierSuccess\Model\ResourceModel
 */
class Supplier extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('os_supplier', 'supplier_id');
    }
}
