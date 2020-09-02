<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Api;


/**
 * Interface DropshipRequestItemRepositoryInterface
 * @package Magestore\DropshipSuccess\Api
 */
interface DropshipRequestItemRepositoryInterface
{
    /**
     * Save dropship request item.
     *
     * @param \Magestore\DropshipSuccess\Api\Data\DropshipRequestItemInterface $dropshipRequestItem
     * @return \Magestore\DropshipSuccess\Api\Data\DropshipRequestItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\DropshipSuccess\Api\Data\DropshipRequestItemInterface $dropshipRequestItem);

    /**
     * Retrieve dropship request item.
     *
     * @param int $dropshipRequestItemId
     * @return \Magestore\DropshipSuccess\Api\Data\DropshipRequestItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($dropshipRequestItemId);

    /**
     * Delete dropship request item.
     *
     * @param \Magestore\DropshipSuccess\Api\Data\DropshipRequestItemInterface $dropshipRequestItem
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magestore\DropshipSuccess\Api\Data\DropshipRequestItemInterface $dropshipRequestItem);

    /**
     * Delete dropship request item by ID.
     *
     * @param int $dropshipRequestItemId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($dropshipRequestItemId);

    /**
     * Cancel dropship request item by ID.
     *
     */
    public function cancelItemById($dropshipRequestItemId);
}