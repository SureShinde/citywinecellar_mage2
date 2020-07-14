<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposStripeTerminal\Model\ResourceModel\ConnectedReader;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magestore\WebposStripeTerminal\Api\Data\ConnectedReaderInterface;

class Collection extends AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = ConnectedReaderInterface::ID;

    /**
     * construct
     */
    public function _construct()
    {
        $this->_init(\Magestore\WebposStripeTerminal\Model\ConnectedReader\ConnectedReader::class, \Magestore\WebposStripeTerminal\Model\ResourceModel\ConnectedReader::class);
    }
}