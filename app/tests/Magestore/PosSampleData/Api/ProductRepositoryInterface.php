<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosSampleData\Api;

interface ProductRepositoryInterface
{
    /**
     * Save Role.
     *
     * @param \Magestore\PosSampleData\Api\Data\ProductInterface $product
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function enableVisibleOnPos(\Magestore\PosSampleData\Api\Data\ProductInterface $product);

}
