<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Api\Data;

/**
 * Interface ShippingChanelInterface
 * @package Magestore\OrderSuccess\Api\Data
 */
interface ShippingChanelInterface
{
    /**
     * {@inheritdoc}
     */
    const BACKORDER  = 'back_order';
    const FULFIL     = 'fulfill';
    const DROPSHIP   = 'dropship';

    /**
     * @return array
     */
    public function getOptionArray();

    /**
     * @return array
     */
    public function getOptionBlockArray();
}