<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magestore\Webpos\Plugin\Inventory\Model\SourceItem\Command;


/**
 * Class SourceItemsSave
 * @package Magestore\Webpos\Plugin\Inventory\Model\SourceItem\Command
 */
class SourceItemsSave
{
    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Inventory\Stock\Item
     */
    protected $stockItemResource;

    /**
     * SourceItemsSave constructor.
     * @param \Magestore\Webpos\Model\ResourceModel\Inventory\Stock\Item $stockItemResource
     */
    public function __construct(
        \Magestore\Webpos\Model\ResourceModel\Inventory\Stock\Item $stockItemResource
    )
    {
        $this->stockItemResource = $stockItemResource;
    }

    /**
     * Update stock updated time after save source items
     *
     * @param \Magento\Inventory\Model\SourceItem\Command\SourceItemsSave $subject
     * @param int $stockId
     * @param \Magento\InventoryApi\Api\Data\SourceItemInterface[] $sourceItems
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute(
        $subject,
        $result = null,
        array $sourceItems
    )
    {
        if (!empty($sourceItems)) {
            $skus = [];
            foreach ($sourceItems as $sourceItem) {
                $skus[] = $sourceItem->getSku();
            }
            if (!empty($skus)) {
                $this->stockItemResource->updateUpdatedTimeBySku($skus);
            }
        }
    }
}
