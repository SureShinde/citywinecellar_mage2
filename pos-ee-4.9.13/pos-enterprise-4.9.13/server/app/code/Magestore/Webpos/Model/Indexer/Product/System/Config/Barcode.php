<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Indexer\Product\System\Config;

class Barcode extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Magestore\Webpos\Model\Indexer\Product\Processor
     */
    protected $_indexerProcessor;
    
    /**
     * @var \Magento\Indexer\Model\Indexer\State
     */
    protected $indexerState;
    
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magestore\Webpos\Model\Indexer\Product\Processor $indexerProcessor
     * @param \Magento\Indexer\Model\Indexer\State $indexerState
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magestore\Webpos\Model\Indexer\Product\Processor $indexerProcessor,
        \Magento\Indexer\Model\Indexer\State $indexerState,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_indexerProcessor = $indexerProcessor;
        $this->indexerState = $indexerState;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }
    
    /**
     * Set after commit callback
     *
     * @return $this
     */
    public function afterSave()
    {
        $this->_getResource()->addCommitCallback([$this, 'processValue']);
        return parent::afterSave();
    }
    
    /**
     * Process barcode attribute change
     *
     * @return void
     */
    public function processValue()
    {
        if ($this->getValue() != $this->getOldValue()) {
            $this->indexerState->loadByIndexer(\Magestore\Webpos\Model\Indexer\Product\Processor::INDEXER_ID);
            $this->indexerState->setStatus(\Magento\Framework\Indexer\StateInterface::STATUS_INVALID);
            $this->indexerState->save();
        }
    }
}
