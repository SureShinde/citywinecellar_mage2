<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Log;

use Magestore\Webpos\Api\Data\Location\LocationInterface;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Bundle\Model\Product\Type as Bundle;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\InventorySalesAdminUi\Model\ResourceModel\GetAssignedStockIdsBySku;

/**
 * Class ProductDeletedRepository
 *
 * @package Magestore\Webpos\Model\Log
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductDeletedRepository implements \Magestore\Webpos\Api\Log\ProductDeletedRepositoryInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var \Magestore\Webpos\Api\Data\Log\ProductDeletedInterface
     */
    protected $productDeleted;
    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Log\ProductDeleted
     */
    protected $productDeletedResource;

    /**
     * @var \Magestore\Webpos\Api\WebposManagementInterface
     */
    protected $webposManagement;

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Location\Location\CollectionFactory
     */
    protected $locationCollectionFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * ProductDeletedRepository constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\Webpos\Api\Data\Log\ProductDeletedInterface $productDeleted
     * @param \Magestore\Webpos\Model\ResourceModel\Log\ProductDeleted $productDeletedResource
     * @param \Magestore\Webpos\Api\WebposManagementInterface $webposManagement
     * @param \Magestore\Webpos\Model\ResourceModel\Location\Location\CollectionFactory $locationCollectionFactory
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\Webpos\Api\Data\Log\ProductDeletedInterface $productDeleted,
        \Magestore\Webpos\Model\ResourceModel\Log\ProductDeleted $productDeletedResource,
        \Magestore\Webpos\Api\WebposManagementInterface $webposManagement,
        \Magestore\Webpos\Model\ResourceModel\Location\Location\CollectionFactory $locationCollectionFactory,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->objectManager = $objectManager;
        $this->productDeleted = $productDeleted;
        $this->productDeletedResource = $productDeletedResource;
        $this->webposManagement = $webposManagement;
        $this->locationCollectionFactory = $locationCollectionFactory;
        $this->resource = $resource;
    }

    /**
     * Insert new product to webpos_product_delete table
     *
     * @param int $productId
     * @return void
     */
    public function insertByProductId($productId)
    {
        if ($this->webposManagement->isMSIEnable()) {
            /** @var \Magestore\Webpos\Model\ResourceModel\Location\Location\Collection $collection */
            $collection = $this->locationCollectionFactory->create();
            $collection->addFieldToFilter(LocationInterface::STOCK_ID, ['gt' => 0]);
            $collection->getSelect()->group(LocationInterface::STOCK_ID);
            $stockIds = $collection->getColumnValues(LocationInterface::STOCK_ID);
            if (!empty($stockIds)) {
                $this->productDeletedResource->deleteByProductId($productId);
                $this->productDeletedResource->insertMultiple($productId, $stockIds);
            }
        } else {
            $this->productDeletedResource->deleteByProductId($productId);
            $this->productDeletedResource->insertMultiple($productId);
        }
    }

    /**
     * Delete product from webpos_product_delete table by id
     *
     * @param int $productId
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteByProductId($productId)
    {
        try {
            $this->productDeletedResource->deleteByProductId($productId);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(__('Unable to delete product deleted'));
        }
    }

    /**
     * Delete product from webpos_product_delete table by product model
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * phpcs:disable Generic.Metrics.NestingLevel
     */
    public function deleteByProduct($product)
    {
        try {
            if ($product->getTypeId() != Grouped::TYPE_CODE
                && $product->getTypeId() != Bundle::TYPE_CODE
                && $product->getTypeId() != Configurable::TYPE_CODE) {
                if ($this->webposManagement->isMSIEnable()) {
                    /** @var \Magestore\Webpos\Model\ResourceModel\Location\Location\Collection $collection */
                    $collection = $this->locationCollectionFactory->create();
                    $collection->addFieldToFilter(LocationInterface::STOCK_ID, ['gt' => 0]);
                    $collection->getSelect()->group(LocationInterface::STOCK_ID);
                    $stockIds = $collection->getColumnValues(LocationInterface::STOCK_ID);
                    if (!empty($stockIds)) {
                        /** @var GetAssignedStockIdsBySku $getAssignedStockIdsBySku */
                        $getAssignedStockIdsBySku = $this->objectManager
                            ->get(GetAssignedStockIdsBySku::class);
                        $assignedStockIds = $getAssignedStockIdsBySku->execute($product->getSku());
                        $inStockIds = $notInStockIds = [];
                        foreach ($stockIds as $stockId) {
                            if (in_array($stockId, $assignedStockIds)) {
                                $inStockIds[] = $stockId;
                            } else {
                                $notInStockIds[] = $stockId;
                            }
                        }
                        if (!empty($inStockIds) || !empty($notInStockIds)) {
                            $this->productDeletedResource->deleteByProductId(
                                $product->getId(),
                                array_merge($inStockIds, $notInStockIds)
                            );
                        }
                        if (!empty($notInStockIds)) {
                            $this->productDeletedResource->insertMultiple($product->getId(), $notInStockIds);
                        }
                    }
                } else {
                    $this->productDeletedResource->deleteByProductId($product->getId());
                }
            } else {
                $this->productDeletedResource->deleteByProductId($product->getId());
            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(__('Unable to delete product deleted'));
        }
    }
}
