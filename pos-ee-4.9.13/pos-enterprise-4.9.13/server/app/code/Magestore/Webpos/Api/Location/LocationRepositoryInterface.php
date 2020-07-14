<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Location;

interface LocationRepositoryInterface
{

    /**
     * get location
     *
     * @param int $id
     * @return \Magestore\Webpos\Api\Data\Location\LocationInterface
     */
    public function getById($id);

    /**
     * get list location
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Location\LocationSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria);

    /**
     * get list all location
     *
     * @return \Magestore\Webpos\Api\Data\Location\LocationInterface[]
     */
    public function getAllLocation();

    /**
     * @param \Magestore\Webpos\Api\Data\Location\LocationInterface $location
     * @return \Magestore\Webpos\Api\Data\Location\LocationInterface
     */
    public function save(\Magestore\Webpos\Api\Data\Location\LocationInterface $location);

    /**
     * @param \Magestore\Webpos\Api\Data\Location\LocationInterface $location
     * @return boolean
     */
    public function delete(\Magestore\Webpos\Api\Data\Location\LocationInterface $location);

    /**
     * @param int $locationId
     * @return boolean
     */
    public function deleteById($locationId);

    /**
     * @param int $staffId
     * @return boolean|\Magestore\Webpos\Api\Data\Location\LocationInterface[]
     */
    public function getListAvailable($staffId);

    /**
     * @param $staffId
     * @return boolean|\Magestore\Webpos\Api\Data\Location\LocationInterface[]
     */
    public function getListLocationWithStaff($staffId);

    /**
     * @param int $stockId
     * @return int[]|null
     */
    public function getLocationIdsByStockId($stockId);
    
    /**
     * Get count location by stock id
     * @param int $stockId
     * @return int
     */
    public function getCountLocationByStockId($stockId);
}