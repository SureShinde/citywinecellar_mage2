<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Api\Data;

/**
 * Interface BatchOrderInterface
 * @package Magestore\OrderSuccess\Api\Data
 */
interface BatchOrderInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const BATCH_ORDER_ID = 'batch_order_id';
    const BATCH_ID = 'batch_id';
    const ORDER_ID = 'order_id';
    
    /**
     * get batch order Id
     *
     * @return int
     */
    public function getBatchOrderId();

    /**
     * set batch order Id
     *
     * @param int $id
     * @return BatchInterface
     */
    public function setBatchOrderId($id);

    /**
     * get batch Id
     *
     * @return int
     */
    public function getBatchId();

    /**
     * set batch Id
     *
     * @param int $id
     * @return BatchInterface
     */
    public function setBatchId($id);

    /**
     * get order Id
     *
     * @return int
     */
    public function getOrderId();

    /**
     * set order Id
     *
     * @param int $id
     * @return BatchInterface
     */
    public function setOrderId($id);

}