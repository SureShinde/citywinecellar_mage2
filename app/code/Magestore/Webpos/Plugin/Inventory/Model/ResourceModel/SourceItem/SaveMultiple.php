<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Plugin\Inventory\Model\ResourceModel\SourceItem;

use Magento\Framework\Registry;
use Magento\Inventory\Model\ResourceModel\SourceItem\SaveMultiple as CoreSaveMultiple;
use Magestore\Webpos\Helper\Data;
use Magestore\Webpos\Model\ResourceModel\Inventory\Stock\Item;
use Psr\Log\LoggerInterface;

/**
 * Reindex product search on elasticsearch server when source items have been updated
 */
class SaveMultiple
{
    /**
     * @var Item
     */
    protected $sourceItemResource;
    /**
     * @var Data
     */
    protected $helper;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * SaveMultiple constructor.
     *
     * @param Item $sourceItemResource
     * @param Data $helper
     * @param LoggerInterface $logger
     * @param Registry $registry
     */
    public function __construct(
        Item $sourceItemResource,
        Data $helper,
        LoggerInterface $logger,
        Registry $registry
    ) {
        $this->sourceItemResource = $sourceItemResource;
        $this->helper = $helper;
        $this->logger = $logger;
        $this->registry = $registry;
    }

    /**
     * Reindex by rows
     *
     * @param CoreSaveMultiple $subject
     * @param array $sourceItems
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecute(
        CoreSaveMultiple $subject,
        array $sourceItems
    ) {
        if (!$this->helper->isEnableElasticSearch()) {
            return;
        }

        $ids = $this->sourceItemResource->reindexBySourceItem($sourceItems);
        try {
            $this->registry->unregister(
                'products_need_to_be_reindexed_pos_search'
            );
            $this->registry->register('products_need_to_be_reindexed_pos_search', $ids);
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
            $this->logger->info($e->getTraceAsString());
        }
    }
}
