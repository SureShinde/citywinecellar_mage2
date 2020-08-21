<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Indexer;

use Magento\Framework\Search\EngineResolverInterface;

/**
 * Class \Magestore\Webpos\Model\Indexer\Product
 */
class Product implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    /**
     * @var \Magestore\Webpos\Model\Indexer\Product\Action\Row
     */
    protected $productFlatIndexerRow;

    /**
     * @var \Magestore\Webpos\Model\Indexer\Product\Action\Rows
     */
    protected $productFlatIndexerRows;

    /**
     * @var \Magestore\Webpos\Model\Indexer\Product\Action\Full
     */
    protected $productFlatIndexerFull;

    /**
     * @var EngineResolverInterface
     */
    protected $engineResolver;

    protected $elasticSearchIndexer;

    /**
     * Product constructor.
     *
     * @param Product\Action\Row $productFlatIndexerRow
     * @param Product\Action\Rows $productFlatIndexerRows
     * @param Product\Action\Full $productFlatIndexerFull
     * @param EngineResolverInterface $engineResolver
     */
    public function __construct(
        \Magestore\Webpos\Model\Indexer\Product\Action\Row $productFlatIndexerRow,
        \Magestore\Webpos\Model\Indexer\Product\Action\Rows $productFlatIndexerRows,
        \Magestore\Webpos\Model\Indexer\Product\Action\Full $productFlatIndexerFull,
        EngineResolverInterface $engineResolver
    ) {
        $this->productFlatIndexerRow = $productFlatIndexerRow;
        $this->productFlatIndexerRows = $productFlatIndexerRows;
        $this->productFlatIndexerFull = $productFlatIndexerFull;
        $this->engineResolver = $engineResolver;
    }

    /**
     * @inheritDoc
     */
    public function executeFull()
    {
        try {
            if ($this->isElasticSearchEnable()) {
                $this->getElasticSearchIndexer()->executeFull();
            } else {
                $this->productFlatIndexerFull->execute();
            }
        } catch (\Exception $e) {
            /** @var \Psr\Log\LoggerInterface $logger */
            $logger = \Magento\Framework\App\ObjectManager::getInstance()
                ->create(\Psr\Log\LoggerInterface::class);
            $logger->info($e->getTraceAsString());
        }
    }

    /**
     * @inheritDoc
     */
    public function executeRow($id)
    {
        if ($this->isElasticSearchEnable()) {
            $this->getElasticSearchIndexer()->executeRow($id);
        } else {
            $this->productFlatIndexerRow->execute($id);
        }
    }

    /**
     * @inheritDoc
     */
    public function executeList(array $ids)
    {
        if ($this->isElasticSearchEnable()) {
            $this->getElasticSearchIndexer()->executeList($ids);
        } else {
            $this->productFlatIndexerRows->execute($ids);
        }
    }

    /**
     * @inheritDoc
     */
    public function execute($ids)
    {
        if ($this->isElasticSearchEnable()) {
            $this->getElasticSearchIndexer()->executeList($ids);
        } else {
            $this->executeList($ids);
        }
    }

    /**
     * Is Elastic Search Enable
     *
     * @return bool
     */
    public function isElasticSearchEnable()
    {
        $currentHandler = $this->engineResolver->getCurrentSearchEngine();
        if (in_array($currentHandler, ['elasticsearch', 'elasticsearch5', 'elasticsearch6', 'elasticsearch7'])) {
            return true;
        }
        return false;
    }

    /**
     * Get Elastic Search Indexer
     *
     * @return \Magestore\Webpos\Model\Indexer\Fulltext
     */
    public function getElasticSearchIndexer()
    {
        if (!$this->elasticSearchIndexer) {
            $data = [
                "indexer_id" => "webpos_productsearch_fulltext",
                "action_class" => \Magestore\Webpos\Model\Indexer\Fulltext::class,
                "title" => "POS Product Search",
                "description" => "Rebuild Catalog product fulltext search index",
                "structure" => \Magestore\Webpos\Model\Indexer\IndexStructure::class
            ];
            $this->elasticSearchIndexer = \Magento\Framework\App\ObjectManager::getInstance()
                ->create(
                    \Magestore\Webpos\Model\Indexer\Fulltext::class,
                    [
                        "data" => $data
                    ]
                );
        }
        return $this->elasticSearchIndexer;
    }
}
