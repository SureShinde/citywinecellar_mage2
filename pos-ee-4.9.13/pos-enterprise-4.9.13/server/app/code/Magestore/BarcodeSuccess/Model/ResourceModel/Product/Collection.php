<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magestore\BarcodeSuccess\Model\ResourceModel\Product;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * Initialize resources
     *
     * @return void
     */
    protected function _construct()
    {
        if ($this->isEnabledFlat()) {
            $this->_init('Magestore\BarcodeSuccess\Model\Product', 'Magento\Catalog\Model\ResourceModel\Product\Flat');
        } else {
            $this->_init('Magestore\BarcodeSuccess\Model\Product', 'Magento\Catalog\Model\ResourceModel\Product');
        }
        $this->_initTables();
    }

}
