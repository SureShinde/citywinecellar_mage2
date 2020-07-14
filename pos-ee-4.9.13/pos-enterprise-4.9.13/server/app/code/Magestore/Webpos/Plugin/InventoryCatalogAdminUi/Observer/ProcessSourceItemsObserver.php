<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Plugin\InventoryCatalogAdminUi\Observer;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Event\Observer as EventObserver;

class ProcessSourceItemsObserver
{
    /**
     * @var \Magestore\Webpos\Api\Log\ProductDeletedRepositoryInterface
     */
    protected $productDeletedRepository;
    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Inventory\Stock\Item
     */
    protected $stockItemResource;

    /**
     * ProcessSourceItemsObserver constructor.
     * @param \Magestore\Webpos\Api\Log\ProductDeletedRepositoryInterface $productDeletedRepository
     * @param \Magestore\Webpos\Model\ResourceModel\Inventory\Stock\Item $stockItemResource
     */
    public function __construct(
        \Magestore\Webpos\Api\Log\ProductDeletedRepositoryInterface $productDeletedRepository,
        \Magestore\Webpos\Model\ResourceModel\Inventory\Stock\Item $stockItemResource
    )
    {
        $this->productDeletedRepository = $productDeletedRepository;
        $this->stockItemResource = $stockItemResource;
    }

    /**
     * @param \Magento\InventoryCatalogAdminUi\Observer\ProcessSourceItemsObserver $subject
     * @param void $result
     * @param EventObserver $observer
     * @return void
     */
    public function arroundExecute(
        $subject,
        callable $proceed,
        EventObserver $observer
    )
    {
        $proceed($observer);
        /** @var ProductInterface $product */
        $product = $observer->getEvent()->getProduct();
        $productId = $product->getId();
        if ($product->getStatus() == \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED
            || $product->getData('webpos_visible') != \Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection::VISIBLE_ON_WEBPOS
        ) {
            if ($productId) {
                $this->productDeletedRepository->insertByProductId($productId);
            }
        } else if ($product->getStatus() == \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
            && $product->getData('webpos_visible') == \Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection::VISIBLE_ON_WEBPOS
        ) {
            $this->productDeletedRepository->deleteByProduct($product);
        }
        $this->stockItemResource->updateUpdatedTimeBySku([$product->getSku()]);
    }
}