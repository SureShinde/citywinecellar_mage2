<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Model\InventoryTransfer\Option;

/**
 * Class Status
 * @package Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option
 */
class Status implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Inventory Transfer status value
     */
    const STATUS_OPEN = 'open';

    const STATUS_CLOSED = 'closed';

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public function getOptionHash()
    {
        return [
            self::STATUS_OPEN => __('Open'),
            self::STATUS_CLOSED => __('Closed')
        ];
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
}