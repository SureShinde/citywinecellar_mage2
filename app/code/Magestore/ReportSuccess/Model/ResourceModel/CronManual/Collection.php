<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */
namespace Magestore\ReportSuccess\Model\ResourceModel\CronManual;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Magestore\ReportSuccess\Model\ResourceModel\CronManual
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';
    /**
     *
     */
    public function _construct()
    {
        $this->_init('Magestore\ReportSuccess\Model\CronManual', 'Magestore\ReportSuccess\Model\ResourceModel\CronManual');
    }
}