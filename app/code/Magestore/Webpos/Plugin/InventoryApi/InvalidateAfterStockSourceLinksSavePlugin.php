<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magestore\Webpos\Plugin\InventoryApi;

use Magento\Framework\Indexer\IndexerRegistry;
use Magento\InventoryApi\Api\StockSourceLinksSaveInterface;
use Magestore\Webpos\Helper\Data;
use Magestore\Webpos\Model\Indexer\Product\Processor;

/**
 * Invalidate index after source links have been saved.
 */
class InvalidateAfterStockSourceLinksSavePlugin
{
    /**
     * @var IndexerRegistry
     */
    private $indexerRegistry;
    /**
     * @var Data
     */
    private $helper;

    /**
     * InvalidateAfterStockSourceLinksSavePlugin constructor.
     *
     * @param IndexerRegistry $indexerRegistry
     * @param Data $helper
     */
    public function __construct(
        IndexerRegistry $indexerRegistry,
        Data $helper
    ) {
        $this->indexerRegistry = $indexerRegistry;
        $this->helper = $helper;
    }

    /**
     * Invalidate index after source links have been saved.
     *
     * @param StockSourceLinksSaveInterface $subject
     * @param void $result
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute(
        StockSourceLinksSaveInterface $subject,
        $result
    ) {
        $indexer = $this->indexerRegistry->get(Processor::INDEXER_ID);
        if ($this->helper->isEnableElasticSearch() && $indexer->isValid()) {
            $indexer->invalidate();
        }
    }
}
