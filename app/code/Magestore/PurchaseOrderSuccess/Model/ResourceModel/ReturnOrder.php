<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\ResourceModel;

/**
 * Resource Model PurchaseOrder
 */
class ReturnOrder extends AbstractResource
{
    const TABLE_RETURN_ORDER = 'os_return_order';

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_RETURN_ORDER, 'return_id');
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
     * Check whether post data is valid
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    public function isValidPostData(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getData('supplier_id') === null
            || $object->getData('returned_at') === null) {
            return false;
        }
        return true;
    }
}
