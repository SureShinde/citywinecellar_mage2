<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Ui\Component\Options;

/**
 * Class CreatedType
 *
 * Used to create created type
 */
class CreatedType extends AbstractOption
{
    /**
     * To option hash
     *
     * @return array
     */
    public function toOptionHash()
    {
        return [
            \Magestore\BarcodeSuccess\Model\History::GENERATED => __('Generated'),
            \Magestore\BarcodeSuccess\Model\History::IMPORTED => __('Imported')
        ];
    }
}
