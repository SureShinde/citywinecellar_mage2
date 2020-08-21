<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\System\Config\Backend;

/**
 * Model config Serialized
 */
class Serialized extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serialize;

    /**
     * After load
     *
     * @return void
     */
    protected function _afterLoad()
    {
        if (!is_array($this->getValue())) {
            $value = $this->getValue();
            $this->setValue(empty($value) ? false : $this->getSerialize()->unserialize($value));
        }
    }

    /**
     * Before save
     *
     * @return $this
     */
    public function beforeSave()
    {
        if (is_array($this->getValue())) {
            $value = $this->getValue();
            unset($value['__empty']);
            $this->setValue($this->getSerialize()->serialize($value));
        }
        return parent::beforeSave();
    }

    /**
     * Get Serialize
     *
     * @return \Magento\Framework\Serialize\SerializerInterface|mixed
     */
    public function getSerialize()
    {
        if (!$this->serialize) {
            $this->serialize = \Magento\Framework\App\ObjectManager::getInstance()
                ->create(\Magento\Framework\Serialize\SerializerInterface::class);
        }
        return $this->serialize;
    }
}
