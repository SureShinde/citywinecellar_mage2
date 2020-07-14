<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Pos;

/**
 * Interface EnterPosInterface
 * @package Magestore\Webpos\Api\Data\Pos
 */
interface EnterPosInterface
{
    /**
     *
     */
    const POS_ID = "pos_id";
    /**
     *
     */
    const LOCATION_ID = 'location_id';

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
     * @return EnterPosInterface
     */
    public function setPosId($posId);
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
     * @return EnterPosInterface
     */
    public function setLocationId($locationId);
}
