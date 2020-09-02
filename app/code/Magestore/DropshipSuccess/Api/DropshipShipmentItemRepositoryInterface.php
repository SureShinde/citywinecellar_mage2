<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Api;

/**
 * Interface DropshipShipmentItemRepositoryInterface
 * @package Magestore\DropshipSuccess\Api
 */
interface DropshipShipmentItemRepositoryInterface
{
    /**
     * Save dropship shipment item.
     *
     * @param \Magestore\DropshipSuccess\Api\Data\DropshipShipmentItemInterface $dropshipShipmentItem
     * @return \Magestore\DropshipSuccess\Api\Data\DropshipShipmentItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\DropshipSuccess\Api\Data\DropshipShipmentItemInterface $dropshipShipmentItem);

    /**
     * Retrieve dropship shipment item.
     *
     * @param int $id
     * @return \Magestore\DropshipSuccess\Api\Data\DropshipShipmentItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($id);

    /**
     * Delete dropship shipment item.
     *
     * @param \Magestore\DropshipSuccess\Api\Data\DropshipShipmentItemInterface $dropshipShipmentItem
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magestore\DropshipSuccess\Api\Data\DropshipShipmentItemInterface $dropshipShipmentItem);

    /**
     * Delete dropship shipment item by ID.
     *
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}