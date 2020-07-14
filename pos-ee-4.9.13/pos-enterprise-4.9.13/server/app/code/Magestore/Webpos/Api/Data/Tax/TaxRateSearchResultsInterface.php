<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Tax;

/**
 * Interface for tax rate search results.
 * @api
 * @since 100.0.2
 */
interface TaxRateSearchResultsInterface extends \Magento\Tax\Api\Data\TaxRateSearchResultsInterface
{
    /**
     * Get items
     *
     * @return \Magestore\Webpos\Api\Data\Tax\TaxRateInterface[]
     */
    public function getItems();

}
