<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Tax;
/**
 * Interface for tax rule search results.
 * @api
 * @since 100.0.2
 */
interface TaxRuleSearchResultsInterface extends \Magento\Tax\Api\Data\TaxRuleSearchResultsInterface
{
    /**
     * Get items
     *
     * @return \Magestore\Webpos\Api\Data\Tax\TaxRuleInterface[]
     */
    public function getItems();
}
