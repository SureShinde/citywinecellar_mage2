<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Model\ResourceModel;

/**
 * Class AdjustStock
 *
 * Adjust stock resource model
 */
class AdjustStock extends AbstractResource
{
    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('os_adjuststock', 'adjuststock_id');
    }

    /**
     * @inheritDoc
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
        if ($object->getData('source_code') === null || $object->getData('adjuststock_code') === null) {
            return false;
        }
        return true;
    }
}
