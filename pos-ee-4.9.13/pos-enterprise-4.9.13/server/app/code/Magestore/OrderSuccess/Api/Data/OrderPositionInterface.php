<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Api\Data;

/**
 * Interface OrderPositionInterface
 * @package Magestore\OrderSuccess\Api\Data
 */
interface OrderPositionInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const NEED_VERIFY = 'needverify';
    const NEED_SHIP = 'needship';
    const AWAITING_PAYMENT = 'awaitingpayment';
    const BACK_ORDER = 'backorder';
    const HOLD = 'hold';
    const CANCELED = 'canceled';

    /**
     * get available order positions
     *
     * @return array
     */
    public function getAvailabeOrderPositions();

    /**
     * get all order positions
     *
     * @return array
     */
    public function getAllOrderPositions();


}