<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Ui\Component\Options;

class CreatedType extends AbstractOption
{
    public function toOptionHash() {
        return array(
            \Magestore\BarcodeSuccess\Model\History::GENERATED => __('Generated'),
            \Magestore\BarcodeSuccess\Model\History::IMPORTED => __('Imported')
        );
    }
}