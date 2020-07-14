<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Api;

/**
 * Interface PackageRepositoryInterface
 * @package Magestore\FulfilSuccess\Api
 */
interface PackageRepositoryInterface
{
    /**
     * Lists packages that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria The search criteria.
     * @return \Magestore\FulfilSuccess\Api\Data\PackRequestSearchResultInterface Pack request search result interface.
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria);

    /**
     * Loads a specified package.
     *
     * @param int $id The package ID.
     * @return \Magestore\FulfilSuccess\Api\Data\PackageInterface Package interface.
     */
    public function get($id);

    /**
     * Loads a specified package by tracking number.
     *
     * @param string $trackingNumber
     * @return \Magestore\FulfilSuccess\Api\Data\PackageInterface Package interface.
     */
    public function getByTrackingNumber($trackingNumber);
    
    /**
     * Deletes a specified package.
     *
     * @param \Magestore\FulfilSuccess\Api\Data\PackageInterface $entity The package.
     * @return bool
     */
    public function delete(\Magestore\FulfilSuccess\Api\Data\PackageInterface $entity);

    /**
     * Performs persist operations for a specified pack request.
     *
     * @param \Magestore\FulfilSuccess\Api\Data\PackageInterface $package The Package.
     * @return \Magestore\FulfilSuccess\Api\Data\PackageInterface Package interface.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\FulfilSuccess\Api\Data\PackageInterface $package);
}