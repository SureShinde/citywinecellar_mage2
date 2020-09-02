<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Model\Source\Adminhtml;

use Magestore\OrderSuccess\Api\Data\OrderPositionInterface;

/**
 * Class OrderPosition
 * @package Magestore\OrderSuccess\Model\Source\Adminhtml
 */
class OrderPosition implements \Magestore\OrderSuccess\Api\Data\OrderPositionInterface
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Need to Verify'), 'value' => OrderPositionInterface::NEED_VERIFY],
            ['label' => __('Need to Ship'), 'value' => OrderPositionInterface::NEED_SHIP],
            ['label' => __('Awaiting Payment'), 'value' => OrderPositionInterface::AWAITING_PAYMENT],
            ['label' => __('Back Sales'), 'value' => OrderPositionInterface::BACK_ORDER],
            ['label' => __('Hold'), 'value' => OrderPositionInterface::HOLD],
            ['label' => __('Canceled'), 'value' => OrderPositionInterface::CANCELED],
        ];
    }

    /**
     * get available order positions
     *
     * @return array
     */
    public function getAvailabeOrderPositions()
    {
        return [
            OrderPositionInterface::NEED_VERIFY,
            OrderPositionInterface::NEED_SHIP,
            OrderPositionInterface::AWAITING_PAYMENT,
            OrderPositionInterface::BACK_ORDER,
        ];
    }

    /**
     * get all order positions
     *
     * @return array
     */
    public function getAllOrderPositions()
    {
        return [
            OrderPositionInterface::NEED_VERIFY,
            OrderPositionInterface::NEED_SHIP,
            OrderPositionInterface::AWAITING_PAYMENT,
            OrderPositionInterface::BACK_ORDER,
            OrderPositionInterface::HOLD,
            OrderPositionInterface::CANCELED
        ];
    }

}