<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magestore\Webpos\Plugin\Inventory\Model\SourceItem\Command;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Registry;
use Magestore\Webpos\Model\Indexer\Product;

/**
 * Plugin to update updated_at of products and also reindex them
 */
class SourceItemsSave
{
    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Inventory\Stock\Item
     */
    protected $stockItemResource;
    /**
     * @var Registry
     */
    protected $registry;
    /**
     * @var Product
     */
    protected $posIndexer;

    /**
     * SourceItemsSave constructor.
     *
     * @param \Magestore\Webpos\Model\ResourceModel\Inventory\Stock\Item $stockItemResource
     * @param Registry $registry
     * @param Product $posIndexer
     */
    public function __construct(
        \Magestore\Webpos\Model\ResourceModel\Inventory\Stock\Item $stockItemResource,
        Registry $registry,
        Product $posIndexer
    ) {
        $this->stockItemResource = $stockItemResource;
        $this->registry = $registry;
        $this->posIndexer = $posIndexer;
    }

    /**
     * Update stock updated time after save source items
     *
     * @param \Magento\Inventory\Model\SourceItem\Command\SourceItemsSave $subject
     * @param void $result
     * @param \Magento\InventoryApi\Api\Data\SourceItemInterface[] $sourceItems
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute(
        $subject,
        $result,
        array $sourceItems
    ) {
        if (!empty($sourceItems)) {
            $skus = [];
            foreach ($sourceItems as $sourceItem) {
                $skus[] = $sourceItem->getSku();
            }
            if (!empty($skus)) {
                $this->stockItemResource->updateUpdatedTimeBySku($skus);
            }
        }

        $productsNeedToReindex = $this->registry->registry(
            'products_need_to_be_reindexed_pos_search'
        );
        $this->registry->unregister(
            'products_need_to_be_reindexed_pos_search'
        );

        if (is_array($productsNeedToReindex) && count($productsNeedToReindex)) {
            try {
                $this->posIndexer->executeList(array_values($productsNeedToReindex));
            } catch (\Exception $e) {
                ObjectManager::getInstance()->create(\Psr\Log\LoggerInterface::class)
                    ->info($e->getTraceAsString());
            }
        }
    }
}
