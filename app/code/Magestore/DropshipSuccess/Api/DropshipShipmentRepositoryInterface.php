<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Api;

/**
 * Interface DropshipShipmentRepositoryInterface
 * @package Magestore\DropshipSuccess\Api
 */
interface DropshipShipmentRepositoryInterface
{
    /**
     * Save dropship shipment.
     *
     * @param \Magestore\DropshipSuccess\Api\Data\DropshipShipmentInterface $dropshipShipment
     * @return \Magestore\DropshipSuccess\Api\Data\DropshipShipmentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\DropshipSuccess\Api\Data\DropshipShipmentInterface $dropshipShipment);

    /**
     * Retrieve dropship shipment.
     *
     * @param int $id
     * @return \Magestore\DropshipSuccess\Api\Data\DropshipShipmentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($id);

    /**
     * Delete dropship shipment.
     *
     * @param \Magestore\DropshipSuccess\Api\Data\DropshipShipmentInterface $dropshipShipment
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magestore\DropshipSuccess\Api\Data\DropshipShipmentInterface $dropshipShipment);

    /**
     * Delete dropship shipment by ID.
     *
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}