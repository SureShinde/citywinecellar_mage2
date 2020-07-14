<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\Item;

/**
 * Class Collection
 * @package Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Invoice
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'return_item_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\PurchaseOrderSuccess\Model\ReturnOrder\Item', 'Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\Item');
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getFirstItem()
    {
        $this->setPageSize(1)->setCurPage(1);
        return parent::getFirstItem();
    }

    /**
     * @param int $returnOrderId
     * @return $this
     */
    public function setReturnOrderToFilter($returnOrderId){
        if($returnOrderId)
            $this->addFieldToFilter('return_id', $returnOrderId);
        return $this;
    }

    public function getProductSelectScanBarcodeCollection($condition = null, $returnId = null){
        $this->getSelect()
            ->columns(['available_qty' => new \Zend_Db_Expr($condition)]);
        if($condition)
            $this->getSelect()->where(new \Zend_Db_Expr($condition) . ' > 0');
        $this->getSelect()->joinLeft(
            ['barcode' => $this->getTable('os_barcode')],
            'main_table.product_id = barcode.product_id',
            ['barcode']
        );
        if($returnId)
            $this->getSelect()->where('main_table.return_id = ?', $returnId);
        return $this;
    }
}