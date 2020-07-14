<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Sales\Order\Data;

class FulfillOnline implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @return array|null
     */
    public function toOptionArray()
    {
        return [
            ['value' => "", 'label' => ""],
            ['value' => 1, 'label' => __("Yes")],
            ['value' => 0, 'label' => __("No")]
        ];
    }
}
