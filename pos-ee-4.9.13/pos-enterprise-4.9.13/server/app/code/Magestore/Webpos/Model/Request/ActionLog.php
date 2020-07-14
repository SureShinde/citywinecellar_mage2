<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Request;

/**
 * Class ActionLog
 *
 * @package Magestore\Webpos\Model\Request
 */
class ActionLog extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_PENDING = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_FAILED = 4;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init(\Magestore\Webpos\Model\ResourceModel\Request\ActionLog::class);
    }
}
