<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model;

use Magento\Framework\Exception\LocalizedException;

/**
 * Giftvoucher Actions Model
 */
class Actions extends \Magento\Framework\DataObject
{

    const ACTIONS_CREATE = 1;
    const ACTIONS_UPDATE = 2;
    const ACTIONS_MASS_UPDATE = 3;
    const ACTIONS_EMAIL = 4;
    const ACTIONS_SPEND_ORDER = 5;
    const ACTIONS_REFUND = 6;
    const ACTIONS_REDEEM = 7;
    const ACTIONS_CANCEL = 8;

    /**
     * Get model option as array
     *
     * @return array
     */
    public function getOptionArray()
    {
        return [
            self::ACTIONS_CREATE => __('Create'),
            self::ACTIONS_UPDATE => __('Update'),
            self::ACTIONS_MASS_UPDATE => __('Mass update'),
            self::ACTIONS_SPEND_ORDER => __('Spent on order'),
            self::ACTIONS_REFUND => __('Refund'),
            self::ACTIONS_REDEEM => __('Redeem'),
            self::ACTIONS_CANCEL => __('Cancel'),
        ];
    }

    /**
     * Get Options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = [];
        foreach ($this->getOptionArray() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $options;
    }

    /**
     * Get Action Label
     *
     * @param int $actionId
     * @return string
     * @throws \Exception
     */
    public function getActionLabel($actionId)
    {
        $optionArray = $this->getOptionArray();
        if (isset($optionArray[$actionId])) {
            return $optionArray[$actionId];
        }
        throw new LocalizedException(__('There is no available gift card history action'));
    }
}
