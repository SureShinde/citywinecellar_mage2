<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Ui\Component\Options;

/**
 * Class TemplateType
 *
 * Used to create template type
 */
class TemplateType extends AbstractOption
{
    /**
     * To option hash
     *
     * @return array
     */
    public function toOptionHash()
    {
        return [
            \Magestore\BarcodeSuccess\Model\Source\TemplateType::STANDARD => __('Standard'),
            \Magestore\BarcodeSuccess\Model\Source\TemplateType::A4 => __('A4'),
            \Magestore\BarcodeSuccess\Model\Source\TemplateType::JEWELRY => __('Jewelry')
        ];
    }
}
