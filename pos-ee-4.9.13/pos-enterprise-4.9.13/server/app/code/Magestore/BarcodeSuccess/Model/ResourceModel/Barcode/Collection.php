<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Model\ResourceModel\Barcode;

use Magento\Framework\DB\Select;

/**
 * Class Collection
 * @package Magestore\BarcodeSuccess\Model\ResourceModel\Barcode
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';
    
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\BarcodeSuccess\Model\Barcode', 'Magestore\BarcodeSuccess\Model\ResourceModel\Barcode');
    }

    /**
     * @return array
     */
    public function getAllProductIds(){
        $this->getSelect()->group('product_id');
        return $this->getColumnValues('product_id');
    }

    /**
     * @return array
     */
    public function getAllBarcodes(){
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Select::ORDER);
        $idsSelect->reset(Select::LIMIT_COUNT);
        $idsSelect->reset(Select::LIMIT_OFFSET);
        $idsSelect->reset(Select::COLUMNS);
        $idsSelect->columns('main_table.barcode');
        return $this->getConnection()->fetchCol($idsSelect);
    }
}