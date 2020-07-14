<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Api\Data\AdjustStock;

interface AdjustStockInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const ADJUSTSTOCK_ID = 'adjuststock_id';
    const ADJUSTSTOCK_CODE = 'adjuststock_code';
    const SOURCE_NAME = 'source_name';
    const SOURCE_CODE = 'source_code';
    const REASON = 'reason';
    const CREATED_BY = 'created_by';
    const CREATED_AT = 'created_at';
    const CONFIRMED_BY = 'confirmed_by';
    const CONFIRMED_AT = 'confirmed_at';
    const STATUS = 'status';
    const KEY_PRODUCTS = 'products';

    /**
     * Constants defined Statuses
     */
    const STATUS_PENDING = 0;
    const STATUS_COMPLETED = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_CANCELED = 3;

    /**
     * Prefix code (using for generate the adjustment code)
     */
    const PREFIX_CODE = 'ADJ';

    /**
     * Get Id
     *
     * @return int|null
     */
    public function getId();

    /**
     * @return int|null
     */
    public function getAdjustStockId();

    /**
     * Get adjuststock code
     *
     * @return string
     */
    public function getAdjustStockCode();

    /**
     * @return string
     */
    public function getSourceCode();

    /**
     * Get Source Name
     *
     * @return string|null
     */
    public function getSourceName();


    /**
     * Get Reason
     *
     * @return string|null
     */
    public function getReason();


    /**
     * @return string
     */
    public function getCreatedBy();


    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @return string
     */
    public function getConfirmedBy();

    /**
     * @return string
     */
    public function getConfirmedAt();

    /**
     * @return int
     */
    public function getStatus();

    /**
     *
     * @param int $id
     * @return AdjustStockInterface
     */
    public function setId($id);

    /**
     *
     * @param string $adjustStockCode
     * @return AdjustStockInterface
     */
    public function setAdjustStockCode($adjustStockCode);

    /**
     *
     * @param string $sourceCode
     * @return AdjustStockInterface
     */
    public function setSourceCode($sourceCode);

    /**
     *
     * @param string $sourceName
     * @return AdjustStockInterface
     */
    public function setSourceName($sourceName);

    /**
     *
     * @param string $reason
     * @return AdjustStockInterface
     */
    public function setReason($reason);

    /**
     *
     * @param string $createdBy
     * @return AdjustStockInterface
     */
    public function setCreatedBy($createdBy);

    /**
     *
     * @param string $createdAt
     * @return AdjustStockInterface
     */
    public function setCreatedAt($createdAt);

    /**
     *
     * @param string $confirmedBy
     * @return AdjustStockInterface
     */
    public function setConfirmedBy($confirmedBy);

    /**
     *
     * @param string $confirmedAt
     * @return AdjustStockInterface
     */
    public function setConfirmedAt($confirmedAt);

    /**
     *
     * @param int $status
     * @return AdjustStockInterface
     */
    public function setStatus($status);

    /**
     * Get adjust stock products.
     *
     * @return \Magestore\AdjustStock\Api\Data\AdjustStock\ProductInterface[]|null
     */
    public function getProducts();

    /**
     * Set adjust stock products.
     *
     * @param \Magestore\AdjustStock\Api\Data\AdjustStock\ProductInterface[] $products
     * @return $this
     */
    public function setProducts(array $products = null);
}
