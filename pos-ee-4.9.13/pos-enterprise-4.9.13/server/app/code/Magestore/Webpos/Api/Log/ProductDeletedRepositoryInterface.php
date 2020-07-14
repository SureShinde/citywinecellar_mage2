<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Log;

/**
 * Interface ProductDeletedRepositoryInterface
 * @package Magestore\Webpos\Api\Log
 */
interface ProductDeletedRepositoryInterface
{
    /**
     * @param int $productId
     * @return void
     */
    public function insertByProductId($productId);

    /**
     * @param int $productId
     * @return boolean
     */
    public function deleteByProductId($productId);

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteByProduct($product);
}