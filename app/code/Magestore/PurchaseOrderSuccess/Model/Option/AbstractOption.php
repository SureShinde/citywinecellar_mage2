<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\Option;

/**
 * Model Option AbstractOption
 */
class AbstractOption implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Status value
     */
    const STATUS_ENABLE = 1;

    const STATUS_DISABLE = 0;

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public function getOptionHash()
    {
        return [self::STATUS_ENABLE => __('Enable'), self::STATUS_DISABLE => __('Disable')];
    }

    /**
     * Get model option hash as array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = [];
        foreach ($this->getOptionHash() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $options;
    }

    /**
     * Return array of options as value-label pairs.
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return $this->getOptionArray();
    }

    /**
     * Return array of options as key-value pairs.
     *
     * @return array Format: array('<key>' => '<value>', '<key>' => '<value>', ...)
     */
    public function toOptionHash()
    {
        return $this->getOptionHash();
    }

    /**
     * Unserialize Array
     *
     * @param string $value
     * @return array
     */
    public function unserializeArray($value)
    {
        if ($value === null) {
            return [];
        }
        if (!is_array($value)) {
            /** @var \Magento\Framework\Serialize\SerializerInterface $serializer */
            $serializer = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Framework\Serialize\SerializerInterface::class);
            return $serializer->unserialize($value);
        }
        return $value;
    }
}
