<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */
namespace Magestore\ReportSuccess\Model\ResourceModel\Bookmark;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Magestore\ReportSuccess\Model\ResourceModel\Bookmark
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
        $this->_init(
            'Magestore\ReportSuccess\Model\Bookmark',
            'Magestore\ReportSuccess\Model\ResourceModel\Bookmark'
        );
    }
}
