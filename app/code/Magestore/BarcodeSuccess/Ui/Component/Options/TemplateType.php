<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Ui\Component\Options;

class TemplateType extends AbstractOption
{
    public function toOptionHash() {
        return array(
            \Magestore\BarcodeSuccess\Model\Source\TemplateType::STANDARD => __('Standard'),
            \Magestore\BarcodeSuccess\Model\Source\TemplateType::A4 => __('A4'),
            \Magestore\BarcodeSuccess\Model\Source\TemplateType::JEWELRY => __('Jewelry')
        );
    }
}