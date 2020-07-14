<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Log;

/**
 * Interface DataLogRepositoryInterface
 * @package Magestore\Webpos\Api\Log
 */
interface DataLogRepositoryInterface {
    /**
     * Retrieve data matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Log\DataLogResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListCustomer(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
    /**
     * Retrieve data matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Log\DataLogResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListCategory(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Retrieve data matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Log\DataLogResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListProduct(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Retrieve data matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Log\DataLogStringResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListOrder(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

}
