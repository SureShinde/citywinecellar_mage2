<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Ui\Component\Options;

class Status extends AbstractOption
{
    public function toOptionHash() {
        return array(
            \Magestore\BarcodeSuccess\Model\Source\Status::ACTIVE => __('Active'),
            \Magestore\BarcodeSuccess\Model\Source\Status::INACTIVE => __('Inactive')
        );
    }
}