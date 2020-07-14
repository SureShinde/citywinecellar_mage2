<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Model\ResourceModel;

class History  extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{    
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('os_barcode_created_history', 'id');
    }
}