<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Rewardpoints\Ui\Component\Listing\Column\Transaction;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Transaction status options
 */
class Status implements OptionSourceInterface
{
    const STATUS_PENDING = 1;
    const STATUS_ON_HOLD = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_CANCELED = 4;
    const STATUS_EXPIRED = 5;
    const ACTION_TYPE_BOTH = 0;
    const ACTION_TYPE_EARN = 1;
    const ACTION_TYPE_SPEND = 2;

    /**
     * To Option Hash
     *
     * @return array
     */
    public function toOptionHash()
    {
        return [
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_ON_HOLD => __('On Hold'),
            self::STATUS_COMPLETED => __('Complete'),
            self::STATUS_CANCELED => __('Canceled'),
            self::STATUS_EXPIRED => __('Expired'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->toOptionHash() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label,
            ];
        }
        return $options;
    }
}
