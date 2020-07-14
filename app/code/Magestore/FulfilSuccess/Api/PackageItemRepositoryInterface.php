<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Api;

use Magento\Sales\Api\ShipmentItemRepositoryInterface;

/**
 * Interface PackageItemRepositoryInterface
 * @package Magestore\FulfilSuccess\Api
 * @api
 */
interface PackageItemRepositoryInterface
{
    /**
     * Lists packages that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria The search criteria.
     * @return \Magestore\FulfilSuccess\Api\Data\PackRequestSearchResultInterface Pack request search result interface.
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria);

    /**
     * Loads a specified package item.
     *
     * @param int $id The package item ID.
     * @return \Magestore\FulfilSuccess\Api\Data\PackageItemInterface Package item interface.
     */
    public function get($id);

    /**
     * Deletes a specified package.
     *
     * @param \Magestore\FulfilSuccess\Api\Data\PackageItemInterface $packageItem The package.
     * @return bool
     */
    public function delete(\Magestore\FulfilSuccess\Api\Data\PackageItemInterface $packageItem);

    /**
     * Performs persist operations for a specified pack request.
     *
     * @param \Magestore\FulfilSuccess\Api\Data\PackageItemInterface $packageItem The Package item.
     * @return \Magestore\FulfilSuccess\Api\Data\PackageItemInterface Package item interface.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\FulfilSuccess\Api\Data\PackageItemInterface $packageItem);
}