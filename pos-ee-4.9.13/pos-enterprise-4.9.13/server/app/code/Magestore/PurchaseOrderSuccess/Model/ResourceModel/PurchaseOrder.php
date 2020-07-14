<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\ResourceModel;

/**
 * Class PurchaseOrder
 * @package Magestore\PurchaseOrderSuccess\Model\ResourceModel
 */
class PurchaseOrder extends AbstractResource
{
    const TABLE_PURCHASE_ORDER = 'os_purchase_order';

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_PURCHASE_ORDER, 'purchase_order_id');
    }

    /**
     * Process post data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$this->isValidPostData($object)) {
            throw new \Magento\Framework\Exception\ValidatorException(
                __('Required field is null')
            );
        }

        return parent::_beforeSave($object);
    }

    /**
     *  Check whether post data is valid
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    public function isValidPostData(\Magento\Framework\Model\AbstractModel $object)
    {
        if (is_null($object->getData('supplier_id')) || is_null($object->getData('purchased_at'))) {
            return false;
        }
        return true;
    }
    
}