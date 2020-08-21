<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magestore\Webpos\Plugin\InventoryApi;

use Magento\Framework\Indexer\IndexerRegistry;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryIndexer\Model\ResourceModel\IsInvalidationRequiredForSource;
use Magestore\Webpos\Helper\Data;
use Magestore\Webpos\Model\Indexer\Product\Processor;

/**
 * Invalidate Inventory Indexer after Source was enabled or disabled.
 */
class InvalidateAfterEnablingOrDisablingSourcePlugin
{
    /**
     * @var IndexerRegistry
     */
    private $indexerRegistry;

    /**
     * @var IsInvalidationRequiredForSource
     */
    private $isInvalidationRequiredForSource;
    /**
     * @var Data
     */
    private $helper;

    /**
     * InvalidateAfterEnablingOrDisablingSourcePlugin constructor.
     *
     * @param IndexerRegistry $indexerRegistry
     * @param IsInvalidationRequiredForSource $isInvalidationRequiredForSource
     * @param Data $helper
     */
    public function __construct(
        IndexerRegistry $indexerRegistry,
        IsInvalidationRequiredForSource $isInvalidationRequiredForSource,
        Data $helper
    ) {
        $this->indexerRegistry = $indexerRegistry;
        $this->isInvalidationRequiredForSource = $isInvalidationRequiredForSource;
        $this->helper = $helper;
    }

    /**
     * Invalidate Inventory Indexer after Source was enabled or disabled.
     *
     * @param SourceRepositoryInterface $subject
     * @param callable $proceed
     * @param SourceInterface $source
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        SourceRepositoryInterface $subject,
        callable $proceed,
        SourceInterface $source
    ) {
        $invalidationRequired = false;
        if ($source->getSourceCode()) {
            $invalidationRequired = $this->isInvalidationRequiredForSource->execute(
                $source->getSourceCode(),
                (bool)$source->isEnabled()
            );
        }

        $proceed($source);

        if ($this->helper->isEnableElasticSearch() && $invalidationRequired) {
            $indexer = $this->indexerRegistry->get(Processor::INDEXER_ID);
            if ($indexer->isValid()) {
                $indexer->invalidate();
            }
        }
    }
}
