<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposStripeTerminal\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magestore\WebposStripeTerminal\Api\Data\ConnectedReaderInterface;

/**
 * Class ConnectedReader
 * @package Magestore\WebposStripeTerminal\Model\ResourceModel
 */
class ConnectedReader extends AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(ConnectedReaderInterface::TABLE_ENTITY,ConnectedReaderInterface::ID);
    }

}
