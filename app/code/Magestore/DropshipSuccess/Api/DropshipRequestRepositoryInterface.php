<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Api;


/**
 * Interface DropshipRequestRepositoryInterface
 * @package Magestore\DropshipSuccess\Api
 */
interface DropshipRequestRepositoryInterface
{
    /**
     * Save dropship request.
     *
     * @param \Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface $dropshipRequest
     * @return \Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface $dropshipRequest);

    /**
     * Retrieve dropship request.
     *
     * @param int $dropshipRequestId
     * @return \Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($dropshipRequestId);

    /**
     * Delete dropship request.
     *
     * @param \Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface $dropshipRequest
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface $dropshipRequest);

    /**
     * Delete dropship request by ID.
     *
     * @param int $dropshipRequestId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($dropshipRequestId);

    /**
     * Cancel dropship request.
     *
     */
    public function cancelDropshipRequest($dropshipRequest);

    /**
     * Check supplier is allowed to access dropship request or not
     * @param \Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface $dropshipRequest
     * @param string|int $supplierId
     * @return bool
     * */
    public function isAllowedAccess(\Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface $dropshipRequest, $supplierId);
}