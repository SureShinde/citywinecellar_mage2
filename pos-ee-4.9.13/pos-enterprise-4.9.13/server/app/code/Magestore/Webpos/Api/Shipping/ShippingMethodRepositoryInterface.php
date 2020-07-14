<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Shipping;

interface ShippingMethodRepositoryInterface
{

    /**
     * Retrieve shipping matching the specified criteria.
     *
     * @return \Magento\Framework\Api\SearchResults
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList();

}
