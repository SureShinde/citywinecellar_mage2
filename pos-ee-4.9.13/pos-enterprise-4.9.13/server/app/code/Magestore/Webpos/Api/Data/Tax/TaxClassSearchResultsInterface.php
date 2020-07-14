<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Tax;

/**
 * Interface for tax class search results.
 * @api
 * @since 100.0.2
 */
interface TaxClassSearchResultsInterface extends \Magento\Tax\Api\Data\TaxClassSearchResultsInterface
{
    /**
     * Get items
     *
     * @return \Magestore\Webpos\Api\Data\Tax\TaxClassInterface[]
     */
    public function getItems();
}
