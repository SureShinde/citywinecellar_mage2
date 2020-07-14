<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\CatalogRule\Product;

/**
 * Interface PriceRepositoryInterface
 * @package Magestore\Webpos\Api\CatalogRule\Product
 */
interface PriceRepositoryInterface
{
    /**
     * Retrieve Catalog rule product price which match a specified criteria.
     *
     * @api
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Webpos\Api\Data\CatalogRule\Product\PriceSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sync(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Retrieve Catalog rule product price ids which match a specified criteria.
     *
     * @api
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Webpos\Api\Data\CatalogRule\Product\PriceIdsSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAllIds(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
