<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Indexer;

use Magento\Framework\Indexer\SaveHandler\IndexerInterface;
use Magento\Framework\Indexer\SaveHandler\Batch;
use Magento\Elasticsearch\Model\Adapter\Elasticsearch as ElasticsearchAdapter;
use Magento\Elasticsearch\Model\Adapter\Index\IndexNameResolver;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Elasticsearch\Model\Adapter\BatchDataMapperInterface;

/**
 * Indexer Handler for Elasticsearch engine.
 *
 * Follow Magento\Elasticsearch\Model\Indexer\IndexerHandler
 */
class IndexerHandler implements IndexerInterface
{
    const DEFAULT_BATCH_SIZE = 500;

    /**
     * @var IndexStructureProxy
     */
    private $indexStructure;

    /**
     * @var ElasticsearchAdapter
     */
    private $adapter;

    /**
     * @var IndexNameResolver
     */
    private $indexNameResolver;

    /**
     * @var Batch
     */
    private $batch;

    /**
     * @var array
     */
    private $data;

    /**
     * @var int
     */
    private $batchSize;

    /**
     * @var ScopeResolverInterface
     */
    private $scopeResolver;
    
    protected $batchDocumentDataMapper;

    /**
     * IndexerHandler constructor.
     *
     * @param IndexStructureProxy $indexStructure
     * @param ElasticsearchAdapter $adapter
     * @param IndexNameResolver $indexNameResolver
     * @param Batch $batch
     * @param ScopeResolverInterface $scopeResolver
     * @param BatchDataMapperInterface $batchDocumentDataMapper
     * @param array $data
     * @param int $batchSize
     */
    public function __construct(
        IndexStructureProxy $indexStructure,
        ElasticsearchAdapter $adapter,
        IndexNameResolver $indexNameResolver,
        Batch $batch,
        ScopeResolverInterface $scopeResolver,
        BatchDataMapperInterface $batchDocumentDataMapper,
        array $data = [],
        $batchSize = self::DEFAULT_BATCH_SIZE
    ) {
        $this->indexStructure = $indexStructure;
        $this->adapter = $adapter;
        $this->indexNameResolver = $indexNameResolver;
        $this->batch = $batch;
        $this->data = $data;
        $this->batchSize = $batchSize;
        $this->scopeResolver = $scopeResolver;
        $this->batchDocumentDataMapper = $batchDocumentDataMapper;
    }

    /**
     * @inheritdoc
     */
    public function saveIndex($dimensions, \Traversable $documents)
    {
        $dimension = current($dimensions);
        $scopeId = $this->scopeResolver->getScope($dimension->getValue())->getId();
        foreach ($this->batch->getItems($documents, $this->batchSize) as $documentsBatch) {
//            $docs = $this->adapter->prepareDocsPerStore($documentsBatch, $scopeId);
            $docs = $this->batchDocumentDataMapper->map(
                $documentsBatch,
                $scopeId,
                ['entityType' => Fulltext::INDEXER_ID]
            );
//            echo '<pre>';var_dump($docs);
            $this->adapter->addDocs($docs, $scopeId, $this->getIndexerId());
        }
        $this->adapter->updateAlias($scopeId, $this->getIndexerId());
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function deleteIndex($dimensions, \Traversable $documents)
    {
        $dimension = current($dimensions);
        $scopeId = $this->scopeResolver->getScope($dimension->getValue())->getId();
        $documentIds = [];
        foreach ($documents as $document) {
            $documentIds[$document] = $document;
        }
        $this->adapter->deleteDocs($documentIds, $scopeId, $this->getIndexerId());
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function cleanIndex($dimensions)
    {
        $this->indexStructure->delete($this->getIndexerId(), $dimensions);
        $this->indexStructure->create($this->getIndexerId(), [], $dimensions);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isAvailable($dimensions = [])
    {
        return $this->adapter->ping();
    }

    /**
     * Returns indexer id.
     *
     * @return string
     */
    private function getIndexerId()
    {
        return $this->indexNameResolver->getIndexMapping($this->data['indexer_id']);
    }
}
