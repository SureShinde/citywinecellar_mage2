<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Log;

/**
 * Interface OrderDeletedInterface
 * @package Magestore\Webpos\Api\Data\Log
 */
interface OrderDeletedInterface {
    /**
     *
     */
    const ID = 'id';
    /**
     *
     */
    const ORDER_ID = 'order_id';
    /**
     *
     */
    const DELETED_AT = 'deleted_at';

    /**
     * Get Id
     *
     * @return int
     */
    public function getId();
    /**
     * Set Id
     *
     * @param int $id
     * @return OrderDeletedInterface
     */
    public function setId($id);

    /**
     * Get order id
     *
     * @return int
     */
    public function getOrderId();
    /**
     * Set order id
     *
     * @param int $productId
     * @return OrderDeletedInterface
     */
    public function setOrderId($productId);
    /**
     * Get deleted at
     *
     * @return string
     */
    public function getDeletedAt();
    /**
     * Set deleted at
     *
     * @param string $deletedAt
     * @return OrderDeletedInterface
     */
    public function setDeletedAt($deletedAt);
}