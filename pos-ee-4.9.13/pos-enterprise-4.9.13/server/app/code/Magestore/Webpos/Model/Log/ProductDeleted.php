<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Log;
/**
 * Class ProductDeleted
 * @package Magestore\Webpos\Model\Log
 */
class ProductDeleted extends \Magento\Framework\Model\AbstractModel implements \Magestore\Webpos\Api\Data\Log\ProductDeletedInterface
{
    /**
     * ProductDeleted constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\Webpos\Model\ResourceModel\Log\ProductDeleted $resource
     * @param \Magestore\Webpos\Model\ResourceModel\Log\ProductDeleted\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\Webpos\Model\ResourceModel\Log\ProductDeleted $resource,
        \Magestore\Webpos\Model\ResourceModel\Log\ProductDeleted\Collection $resourceCollection,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Get Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Set Id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get product id
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * Set product id
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * Get deleted at
     *
     * @return string
     */
    public function getDeletedAt()
    {
        return $this->getData(self::DELETED_AT);
    }

    /**
     * Set deleted at
     *
     * @param string $deletedAt
     * @return $this
     */
    public function setDeletedAt($deletedAt)
    {
        return $this->setData(self::DELETED_AT, $deletedAt);
    }

    /**
     * Get stock id
     *
     * @return int|null
     */
    public function getStockId()
    {
        return $this->getData(self::STOCK_ID);
    }

    /**
     * Set stock id
     *
     * @param int|null $stockId
     * @return ProductDeletedInterface
     */
    public function setStockId($stockId)
    {
        return $this->setData(self::STOCK_ID, $stockId);
    }
}
