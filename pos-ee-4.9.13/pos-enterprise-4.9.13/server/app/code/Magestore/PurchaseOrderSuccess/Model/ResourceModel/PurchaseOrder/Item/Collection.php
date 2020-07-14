<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item;

/**
 * Class Collection
 * @package Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Invoice
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'purchase_order_item_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Item', 'Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item');
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
     * @param int $purchaseOrderId
     * @return $this
     */
    public function setPurchaseOrderToFilter($purchaseOrderId){
        if($purchaseOrderId)
            $this->addFieldToFilter('purchase_order_id', $purchaseOrderId);
        return $this;
    }

    public function getProductSelectScanBarcodeCollection($condition = null, $purchaseId = null){
        $this->getSelect()
            ->columns(['available_qty' => new \Zend_Db_Expr($condition)]);
        if($condition)
            $this->getSelect()->where(new \Zend_Db_Expr($condition) . ' > 0');
        $this->getSelect()->joinLeft(
            ['barcode' => $this->getTable('os_barcode')],
            'main_table.product_id = barcode.product_id',
            ['barcode']
        );
        if($purchaseId)
            $this->getSelect()->where('main_table.purchase_order_id = ?', $purchaseId);
        return $this;
    }
}