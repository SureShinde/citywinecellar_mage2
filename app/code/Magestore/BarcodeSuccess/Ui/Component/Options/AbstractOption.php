<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Ui\Component\Options;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class AbstractOption
 *
 * To create abstract option
 */
class AbstractOption implements OptionSourceInterface
{
    /**
     * To option hash
     *
     * @return array
     */
    public function toOptionHash()
    {
        return [];
    }

    /**
     * To option array
     *
     * @return array
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
