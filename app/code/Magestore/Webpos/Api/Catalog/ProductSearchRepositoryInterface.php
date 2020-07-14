<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Catalog;

/**
 * @api
 */
interface ProductSearchRepositoryInterface extends ProductRepositoryInterface
{
    /**
     * Get webpos search attributes
     *
     * @return mixed[]
     */
    public function getSearchAttributes();

    /**
     * Search product
     *
     * @param \Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Catalog\SimpleProductSearchResultsInterface
     */
    public function search(\Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Search product barcode
     *
     * @param \Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Catalog\ProductSearchResultsInterface
     */
    public function barcode(\Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria);
}
