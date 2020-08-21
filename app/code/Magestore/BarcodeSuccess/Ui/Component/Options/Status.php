<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Ui\Component\Options;

/**
 * Class Status
 *
 * Used to create status option
 */
class Status extends AbstractOption
{
    /**
     * To option hash
     *
     * @return array
     */
    public function toOptionHash()
    {
        return [
            \Magestore\BarcodeSuccess\Model\Source\Status::ACTIVE => __('Active'),
            \Magestore\BarcodeSuccess\Model\Source\Status::INACTIVE => __('Inactive')
        ];
    }
}
