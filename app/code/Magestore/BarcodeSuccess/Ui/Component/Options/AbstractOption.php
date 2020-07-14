<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Ui\Component\Options;

use Magento\Framework\Data\OptionSourceInterface;

class AbstractOption implements OptionSourceInterface
{
    public function toOptionHash() {
        return array(
        );
    }
    public function toOptionArray() {
        $options = array();
        foreach ($this->toOptionHash() as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label,
            );
        }
        return $options;
    }
}