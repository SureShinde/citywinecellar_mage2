<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magestore\Webpos\Plugin\InventoryApi;

use Magento\Framework\Indexer\IndexerRegistry;
use Magento\InventoryApi\Api\StockSourceLinksDeleteInterface;
use Magestore\Webpos\Helper\Data;
use Magestore\Webpos\Model\Indexer\Product\Processor;

/**
 * Invalidate index after source links have been deleted.
 */
class InvalidateAfterStockSourceLinksDeletePlugin
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
     * InvalidateAfterStockSourceLinksDeletePlugin constructor.
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
     * After execute
     *
     * @param StockSourceLinksDeleteInterface $subject
     * @param void $result
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute(
        StockSourceLinksDeleteInterface $subject,
        $result
    ) {
        $indexer = $this->indexerRegistry->get(Processor::INDEXER_ID);
        if ($this->helper->isEnableElasticSearch() && $indexer->isValid()) {
            $indexer->invalidate();
        }
    }
}
