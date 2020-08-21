<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Model\Source;

/**
 * Giftvoucher Typeofbarcode Model
 *
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class TypeOfBarcode implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $positions = [
            'code128' => __('Code 128'),
            'qr' => __('QR code'),
        ];
        $options = [];
        foreach ($positions as $code => $label) {
            $options[] = [
                'value' => $code,
                'label' => $label
            ];
        }
        return $options;
    }
}
