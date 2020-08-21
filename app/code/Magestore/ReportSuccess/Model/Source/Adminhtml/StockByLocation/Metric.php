<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\Source\Adminhtml\StockByLocation;

/**
 * Stock by location - Metric
 */
class Metric implements \Magento\Framework\Option\ArrayInterface
{
    const QTY_ON_HAND = 'qty_on_hand';
    const AVAILABLE_QTY = 'available_qty';
    const QTY_TO_SHIP = 'qty_to_ship';
    const INVENTORY_VALUE = 'stock_value';
    const POTENTIAL_REVENUE = 'potential_revenue';
    const PROFIT_VALUE = 'potential_profit';

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $options = [
            ['value' => self::QTY_ON_HAND, 'label' => __('Qty On-hand')],
            ['value' => self::AVAILABLE_QTY, 'label' => __('Available Qty')],
            ['value' => self::QTY_TO_SHIP, 'label' => __('Qty To Ship')],
            ['value' => self::INVENTORY_VALUE, 'label' => __('Stock Value')],
            ['value' => self::POTENTIAL_REVENUE, 'label' => __('Potential Revenue')],
            ['value' => self::PROFIT_VALUE, 'label' => __('Potential Profit')]
        ];
        return $options;
    }

    /**
     * To Option List Array
     *
     * @return array
     */
    public function toOptionListArray()
    {
        $options = [];
        $options[self::QTY_ON_HAND] = __('Qty On-hand');
        $options[self::AVAILABLE_QTY] = __('Available Qty');
        $options[self::QTY_TO_SHIP] = __('Qty To Ship');
        $options[self::INVENTORY_VALUE] = __('Inventory Value');
        $options[self::POTENTIAL_REVENUE] = __('Potential Revenue');
        $options[self::PROFIT_VALUE] = __('Profit Value');
        return $options;
    }
}
