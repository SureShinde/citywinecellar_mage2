<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


declare(strict_types=1);

namespace Amasty\Xlanding\Observer;

use Amasty\Xlanding\Model\Indexer\AbstractIndexer;
use Amasty\Xlanding\Model\Indexer\ProductPage;
use Magento\Catalog\Model\Product;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Indexer\IndexerInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Indexer\Model\Indexer;

class CatalogProductSaveAfter implements ObserverInterface
{
    /**
     * @var IndexerRegistry
     */
    private $indexerRegistry;

    /**
     * @var null|int
     */
    private $currentProductId = null;

    public function __construct(
        IndexerRegistry $indexerRegistry
    ) {
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        if (!$this->getIndexer()->isScheduled()) {
            /** @var Product $product */
            $product = $observer->getEvent()->getProduct();
            $this->currentProductId = $product->getId();
            $product->getResource()->addCommitCallback([$this, 'reindex']);
        }

        return $this;
    }

    public function reindex()
    {
        if ($this->currentProductId) {
            $this->getIndexer()->setData(AbstractIndexer::FORCED_FLAG, true);
            $this->getIndexer()->reindexRow($this->currentProductId);
            $this->getIndexer()->setData(AbstractIndexer::FORCED_FLAG, false);
            $this->currentProductId = null;
        }
    }

    /**
     * @return IndexerInterface|Indexer
     */
    private function getIndexer(): IndexerInterface
    {
        return $this->indexerRegistry->get(ProductPage::INDEXER_ID);
    }
}
