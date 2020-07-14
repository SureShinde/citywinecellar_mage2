<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Staff\Login;

/**
 * @api
 */
/**
 * Interface PosResultInterface
 * @package Magestore\Webpos\Api\Data\Staff\Login
 */
interface PosResultInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const POS_ID = 'pos_id';
    const POS_NAME = 'pos_name';
    const STAFF_ID = 'staff_id';
    const STATUS = 'status';
    const STAFF_NAME = 'staff_name';

    /**
     * Get pos id
     *
     * @api
     * @return int
     */
    public function getPosId();

    /**
     * Set pos id
     *
     * @api
     * @param int $posId
     * @return PosResultInterface
     */
    public function setPosId($posId);

    /**
     * Get pos name
     *
     * @api
     * @return string
     */
    public function getPosName();

    /**
     * Set pos name
     *
     * @api
     * @param string $posName
     * @return PosResultInterface
     */
    public function setPosName($posName);

    /**
     * Get staff id
     *
     * @api
     * @return int
     */
    public function getStaffId();

    /**
     * Set staff id
     *
     * @api
     * @param int $staffId
     * @return PosResultInterface
     */
    public function setStaffId($staffId);

    /**
     * Get status
     *
     * @api
     * @return int
     */
    public function getStatus();

    /**
     * Set status
     *
     * @api
     * @param int $status
     * @return PosResultInterface
     */
    public function setStatus($status);

    /**
     * Get staff name
     *
     * @api
     * @return string
     */
    public function getStaffName();

    /**
     * Set staff name
     *
     * @api
     * @param int $staffName
     * @return PosResultInterface
     */
    public function setStaffName($staffName);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Staff\Login\PosResultExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Staff\Login\PosResultExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Staff\Login\PosResultExtensionInterface $extensionAttributes
    );
}
