<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Model;

use Magento\Framework\Model\AbstractModel;
use Magestore\AdjustStock\Api\Data\AdjustStock\AdjustStockInterface;

/**
 * Class AdjustStock
 * @package Magestore\AdjustStock\Model
 */
class AdjustStock extends AbstractModel implements AdjustStockInterface
{
    /**
     * @var \Magestore\AdjustStock\Model\AdjustStock\ProductFactory
     */
    protected $_adjuststockProductFactory;

    /**
     * @var \Magestore\AdjustStock\Model\ResourceModel\AdjustStock\Product\CollectionFactory
     */
    protected $adjuststockProductCollectionFactory;

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct();
        $this->_init('Magestore\AdjustStock\Model\ResourceModel\AdjustStock');
    }

    /**
     * AdjustStock constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param AdjustStock\ProductFactory $adjustStockProductFactory
     * @param ResourceModel\AdjustStock\Product\CollectionFactory $adjuststockProductCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\AdjustStock\Model\AdjustStock\ProductFactory $adjustStockProductFactory,
        \Magestore\AdjustStock\Model\ResourceModel\AdjustStock\Product\CollectionFactory $adjuststockProductCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ){
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_adjuststockProductFactory = $adjustStockProductFactory;
        $this->adjuststockProductCollectionFactory = $adjuststockProductCollectionFactory;
    }


    /**
     * @inheritDoc
     */
    public function getStockActivityProductModel() {
        return $this->_adjuststockProductFactory->create();
    }

    /**
     * @inheritDoc
     */
    public function getAdjustStockId()
    {
        return $this->getData(self::ADJUSTSTOCK_ID);
    }

    /**
     * @inheritDoc
     */
    public function getAdjustStockCode()
    {
        return $this->getData(self::ADJUSTSTOCK_CODE);
    }

    /**
     * @inheritDoc
     */
    public function getConfirmedAt()
    {
        return $this->getData(self::CONFIRMED_AT);
    }

    /**
     * @inheritDoc
     */
    public function getConfirmedBy()
    {
        return $this->getData(self::CONFIRMED_BY);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedBy()
    {
        return $this->getData(self::CREATED_BY);
    }

    /**
     * @inheritDoc
     */
    public function getReason()
    {
        return $this->getData(self::REASON);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function getSourceCode()
    {
        return $this->getData(self::SOURCE_CODE);
    }

    /**
     * @inheritDoc
     */
    public function getSourceName()
    {
        return $this->getData(self::SOURCE_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setAdjustStockCode($adjustStockCode)
    {
        return $this->setData(self::ADJUSTSTOCK_CODE, $adjustStockCode);
    }

    /**
     * @inheritDoc
     */
    public function setConfirmedAt($confirmedAt)
    {
        return $this->setData(self::CONFIRMED_AT, $confirmedAt);
    }

    /**
     * @inheritDoc
     */
    public function setConfirmedBy($confirmedBy)
    {
        return $this->setData(self::CONFIRMED_BY, $confirmedBy);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedBy($createdBy)
    {
        return $this->setData(self::CREATED_BY, $createdBy);
    }

    /**
     * @inheritDoc
     */
    public function setReason($reason)
    {
        return $this->setData(self::REASON, $reason);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function setSourceName($sourceName)
    {
        return $this->setData(self::SOURCE_NAME, $sourceName);
    }

    /**
     * @inheritDoc
     */
    public function setSourceCode($sourceCode)
    {
        return $this->setData(self::SOURCE_CODE, $sourceCode);
    }

    /**
     * get product collection
     *
     * @return \Magestore\AdjustStock\Model\ResourceModel\AdjustStock\Product\Collection
     */
    public function getProductCollection()
    {
        return $this->adjuststockProductCollectionFactory->create()
            ->addFieldToFilter('adjuststock_id', ['eq' => $this->getId()]);
    }

    /**
     * @inheritDoc
     */
    public function getProducts()
    {
        $adjustStockId = $this->getId();
        /** @var \Magestore\AdjustStock\Model\ResourceModel\AdjustStock\Product\Collection $products */
        $products = $this->adjuststockProductCollectionFactory->create()->addFieldToFilter('adjuststock_id', ['eq' => $adjustStockId]);
        return $products->getData();
    }

    /**
     * @inheritDoc
     */
    public function setProducts(array $products = null)
    {
        /** @var \Magestore\AdjustStock\Api\Data\AdjustStock\ProductInterface $product */
//        foreach ($products as $product) {
//            $existingAdjustStockProduct = $this->getProduct($product->getAdjuststockProductId());
//            try {
//                /** @var \Magestore\AdjustStock\Model\AdjustStock\Product $newAdjustStockProduct */
//                $newAdjustStockProduct = $this->_adjuststockProductFactory->create();
//                if ($existingAdjustStockProduct->getId()) {
//                    $newAdjustStockProduct->setIdFieldName($existingAdjustStockProduct->getId());
//                }
//                $newAdjustStockProduct
//                    ->setAdjuststockId($this->getId())
//                    ->setProductId($product->getProductId())
//                    ->setProductName($product->getProductName())
//                    ->setProductSku($product->getProductSku())
//                    ->setOldQty($product->getOldQty())
//                    ->setSuggestQty($product->getSuggestQty())
//                    ->setAdjustQty($product->getAdjustQty())
//                    ->save();
//                return $this->getProduct($newAdjustStockProduct->getAdjuststockProductId());
//            } catch (\Exception $e) {
//                $this->logger->log($e->getMessage(), 'apiCreateAdjustStockProduct');
//            }
//        }
    }
}
