<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\System\Source;

class SalesPeriod implements \Magento\Framework\Option\ArrayInterface
{
    const LAST_7_DAYS_VALUE = 'last_7_days';
    const LAST_30_DAYS_VALUE = 'last_30_days';
    const LAST_3_MONTHS_VALUE = 'last_3_months';
    const LAST_7_DAYS_LABEL = 'Last 7 days';
    const LAST_30_DAYS_LABEL = 'Last 30 days';
    const LAST_3_MONTHS_LABEL = 'Last 3 months';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::LAST_7_DAYS_VALUE, 'label' => __(self::LAST_7_DAYS_LABEL)], 
            ['value' => self::LAST_30_DAYS_VALUE, 'label' => __(self::LAST_30_DAYS_LABEL)],
            ['value' => self::LAST_3_MONTHS_VALUE, 'label' => __(self::LAST_3_MONTHS_LABEL)]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::LAST_7_DAYS_VALUE => __(self::LAST_7_DAYS_LABEL), 
            self::LAST_30_DAYS_VALUE => __(self::LAST_30_DAYS_LABEL),
            self::LAST_3_MONTHS_VALUE => __(self::LAST_3_MONTHS_LABEL)
        ];
    }
}
