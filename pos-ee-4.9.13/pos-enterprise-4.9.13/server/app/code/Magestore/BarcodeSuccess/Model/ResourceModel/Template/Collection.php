<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Model\ResourceModel\Template;

/**
 * Class Collection
 * @package Magestore\BarcodeSuccess\Model\ResourceModel\Template
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'template_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\BarcodeSuccess\Model\Template', 'Magestore\BarcodeSuccess\Model\ResourceModel\Template');
    }
}