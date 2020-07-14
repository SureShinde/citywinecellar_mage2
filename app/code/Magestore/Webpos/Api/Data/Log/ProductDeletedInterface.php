<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Log;

/**
 * Interface ProductDeletedInterface
 * @package Magestore\Webpos\Api\Data\Log
 */
interface ProductDeletedInterface {
    /**
     *
     */
    const ID = 'id';
    /**
     *
     */
    const PRODUCT_ID = 'product_id';
    /**
     *
     */
    const DELETED_AT = 'deleted_at';
    
    const STOCK_ID = 'stock_id';

    /**
     * Get Id
     *
     * @return int
     */
    public function getId();
    /**
     * Set Id
     *
     * @param int $id
     * @return ProductDeletedInterface
     */
    public function setId($id);

    /**
     * Get product id
     *
     * @return int
     */
    public function getProductId();
    /**
     * Set product id
     *
     * @param int $productId
     * @return ProductDeletedInterface
     */
    public function setProductId($productId);
    /**
     * Get deleted at
     *
     * @return string
     */
    public function getDeletedAt();
    /**
     * Set deleted at
     *
     * @param string $deletedAt
     * @return ProductDeletedInterface
     */
    public function setDeletedAt($deletedAt);

    /**
     * Get stock id
     *
     * @return int|null
     */
    public function getStockId();
    /**
     * Set stock id
     *
     * @param int|null $stockId
     * @return ProductDeletedInterface
     */
    public function setStockId($stockId);
}