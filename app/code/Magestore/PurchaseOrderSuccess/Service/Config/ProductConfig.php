<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Service\Config;

/**
 * Class TaxAndShipping
 * @package Magestore\PurchaseOrderSuccess\Service\Config
 */
class ProductConfig extends AbstractConfig
{
    const DEFAULT_SOURCE_PRODUCTS = 'purchaseordersuccess/product_config/products_from';

    /**
     * @return int
     */
    public function getProductSource(){
        return $this->scopeConfig->getValue(self::DEFAULT_SOURCE_PRODUCTS);
    }
}