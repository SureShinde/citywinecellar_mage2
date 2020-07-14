<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel\Log;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CustomerDeleted extends AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('webpos_customer_deleted','id');
    }

}
