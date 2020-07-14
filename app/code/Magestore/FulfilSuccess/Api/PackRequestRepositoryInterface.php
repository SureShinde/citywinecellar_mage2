<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Api;

/**
 * Interface PackRequestRepositoryInterface
 * @package Magestore\FulfilSuccess\Api
 * @api
 */
interface PackRequestRepositoryInterface
{
    /**
     * Lists pack requests that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria The search criteria.
     * @return \Magestore\FulfilSuccess\Api\Data\PackRequestSearchResultInterface Pack request search result interface.
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria);

    /**
     * Loads a specified pack request.
     *
     * @param int $id The pack request ID.
     * @return \Magestore\FulfilSuccess\Api\Data\PackRequestInterface Pack Request interface.
     */
    public function get($id);
    

    /**
     * Get list of Pack Requests by Sales Id
     * 
     * @param int $orderId
     * @return Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface[]
     */
    public function getByOrderId($orderId);    

    /**
     * Get Item in Pack Request
     * 
     * @param \Magestore\FulfilSuccess\Api\Data\PackRequestInterface $packRequest
     * @param int $itemId
     * @return \Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface
     */
    public function getItem(\Magestore\FulfilSuccess\Api\Data\PackRequestInterface $packRequest, $itemId);

    /**
     * Deletes a specified pack request.
     *
     * @param \Magestore\FulfilSuccess\Api\Data\PackRequestInterface $packRequest The pack request.
     * @return bool
     */
    public function delete(\Magestore\FulfilSuccess\Api\Data\PackRequestInterface $packRequest);

    /**
     * Performs persist operations for a specified pack request.
     *
     * @param \Magestore\FulfilSuccess\Api\Data\PackRequestInterface $entity The Pack request.
     * @return \Magestore\FulfilSuccess\Api\Data\PackRequestInterface Pack Request interface.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\FulfilSuccess\Api\Data\PackRequestInterface $packRequest);

    /**
     * Retrieve items list from PackRequest
     *
     * @param \Magestore\FulfilSuccess\Api\Data\PackRequestInterface $packRequest
     * @return \Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getItemList(Data\PackRequestInterface $packRequest);

    /**
     * Get pack request by pick request Id
     *
     * @param int $pickRequestPick
     * @return \Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface
     */
    public function getByPickRequestId($pickRequestPick);
}