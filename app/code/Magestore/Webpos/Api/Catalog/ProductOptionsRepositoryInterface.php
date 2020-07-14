<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Catalog;

/**
 * @api
 */
interface ProductOptionsRepositoryInterface
{
    /**
     * @param int $product_id
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\ProductOptionsInterface
     */
    public function getProductOptions($product_id);
}