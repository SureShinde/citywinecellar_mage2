<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposHeartlandTerminal\Model\ResourceModel\ConnectedReader;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magestore\WebposHeartlandTerminal\Api\Data\ConnectedReaderInterface;

/**
 * Class Collection
 *
 * @package Magestore\WebposHeartlandTerminal\Model\ResourceModel\ConnectedReader
 */
class Collection extends AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = ConnectedReaderInterface::ID;
    /**
     * Construct
     */
    public function _construct()
    {
        $this->_init(
            \Magestore\WebposHeartlandTerminal\Model\ConnectedReader\ConnectedReader::class,
            \Magestore\WebposHeartlandTerminal\Model\ResourceModel\ConnectedReader::class
        );
    }
}
