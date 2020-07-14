<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\Option;

/**
 * Class AbstractOption
 * @package Magestore\PurchaseOrderSuccess\Model\Option
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
     * get model option hash as array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = [];
        foreach ($this->getOptionHash() as $value => $label) {
            $options[] = [
                'value'    => $value,
                'label'    => $label
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
     * @param string $value
     * @return array
     */
    public function unserializeArray($value){
        if(!is_array($value)){
            return unserialize($value); 
        }
        return $value;
    }
}