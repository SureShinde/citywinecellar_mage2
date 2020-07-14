<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\System\Config\Backend;

class Serialized extends \Magento\Framework\App\Config\Value
{
    /**
     * @return void
     */
    protected function _afterLoad()
    {
        if (!is_array($this->getValue())) {
            $value = $this->getValue();
            $this->setValue(empty($value) ? false : unserialize($value));
        }
    }

    /**
     * @return $this
     */
    public function beforeSave()
    {
        if (is_array($this->getValue())) {
            $value = $this->getValue();
            unset($value['__empty']);
            $this->setValue(serialize($value));
        }
        return parent::beforeSave();
    }
}
