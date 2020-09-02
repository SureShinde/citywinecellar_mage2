<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Model\ResourceModel;

/**
 * Class DropshipRequest
 * @package Magestore\DropshipSuccess\Model\ResourceModel
 */
class DropshipRequest extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('os_dropship_request', 'dropship_request_id');
    }
}