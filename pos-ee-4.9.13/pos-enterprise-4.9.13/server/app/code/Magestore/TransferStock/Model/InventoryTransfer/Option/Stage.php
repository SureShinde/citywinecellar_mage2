<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Model\InventoryTransfer\Option;

/**
 * Class Stage
 * @package Magestore\TransferStock\Model\InventoryTransfer\Option
 */
class Stage implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Inventory Transfer status value
     */
    const STAGE_NEW = 'new';

    const STAGE_SENT = 'sent';

    const STAGE_RECEIVING = 'receiving';

    const STAGE_COMPLETED = 'completed';

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public function getOptionHash()
    {
        return [
            self::STAGE_NEW => __('New'),
            self::STAGE_SENT => __('Sent'),
            self::STAGE_RECEIVING => __('Receiving'),
            self::STAGE_COMPLETED => __('Completed')
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