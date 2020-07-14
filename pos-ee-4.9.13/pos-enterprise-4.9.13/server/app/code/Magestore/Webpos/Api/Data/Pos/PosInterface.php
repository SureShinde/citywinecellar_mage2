<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Pos;

/**
 * Interface PosInterface
 * @package Magestore\Webpos\Api\Data\Pos
 */
interface PosInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     *
     */
    const POS_ID = "pos_id";
    /**
     *
     */
    const POS_NAME = 'pos_name';
    /**
     *
     */
    const LOCATION_ID = 'location_id';
    /**
     *
     */
    const STAFF_ID = 'staff_id';
    /**
     *
     */
    const STATUS = 'status';
    /**
     *
     */
    const DENOMINATION_IDS = 'denomination_ids';


    /**
     * Get pos id
     *
     * @api
     * @return string|null
     */
    public function getPosId();

    /**
     * Set pos id
     *
     * @api
     * @param string $posId
     * @return PosInterface
     */
    public function setPosId($posId);
    /**
     * Get pos name
     *
     * @api
     * @return string|null
     */
    public function getPosName();

    /**
     * Set pos name
     *
     * @api
     * @param string $posName
     * @return PosInterface
     */
    public function setPosName($posName);
    /**
     * Get location id
     *
     * @api
     * @return string|null
     */
    public function getLocationId();

    /**
     * Set location id
     *
     * @api
     * @param string $locationId
     * @return PosInterface
     */
    public function setLocationId($locationId);
    /**
     * Get staff id
     *
     * @api
     * @return string|null
     */
    public function getStaffId();

    /**
     * Set staff id
     *
     * @api
     * @param string $staffId
     * @return PosInterface
     */
    public function setStaffId($staffId);
    /**
     * Get status
     *
     * @api
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     *
     * @api
     * @param string $status
     * @return PosInterface
     */
    public function setStatus($status);

    /**
     * Get denomination ids
     *
     * @api
     * @return string|null
     */
    public function getDenominationIds();

    /**
     * Set denomination ids
     *
     * @api
     * @param string $denominationIds
     * @return PosInterface
     */
    public function setDenominationIds($denominationIds);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Pos\PosExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Pos\PosExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Pos\PosExtensionInterface $extensionAttributes
    );
}
