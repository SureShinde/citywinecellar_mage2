<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosSampleData\Api\Data;

/**
 * @api
 */
interface ProductInterface
{
    const SKU = 'sku';

    /**
     * Get pos name
     *
     * @api
     * @return string
     */
    public function getSku();

    /**
     * Set pos name
     *
     * @api
     * @param string $sku
     * @return \Magestore\PosSampleData\Api\Data\ProductInterface
     */
    public function setSku($sku);

}
