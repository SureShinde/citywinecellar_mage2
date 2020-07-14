<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Log;

/**
 * Interface CustomerDeletedInterface
 * @package Magestore\Webpos\Api\Data\Log
 */
interface CustomerDeletedInterface {
    /**
     *
     */
    const ID = 'id';
    /**
     *
     */
    const CUSTOMER_ID = 'customer_id';
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
     * @return CustomerDeletedInterface
     */
    public function setId($id);

    /**
     * Get customer id
     *
     * @return int
     */
    public function getCustomerId();
    /**
     * Set customer id
     *
     * @param int $customerId
     * @return CustomerDeletedInterface
     */
    public function setCustomerId($customerId);
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
     * @return CustomerDeletedInterface
     */
    public function setDeletedAt($deletedAt);
}