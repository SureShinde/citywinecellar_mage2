<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Indexer\Product;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class Observer implements ObserverInterface
{
    /**
     * indexer processor
     *
     * @var \Magestore\Webpos\Model\Indexer\Product\Processor
     */
    protected $_indexerProcessor;
    
    /**
     * @param \Magestore\Webpos\Model\Indexer\Product\Processor $indexerProcessor
     */
    public function __construct(\Magestore\Webpos\Model\Indexer\Product\Processor $indexerProcessor)
    {
        $this->_indexerProcessor = $indexerProcessor;
    }
    
    /**
     * {@inheritDoc}
     */
    public function execute(EventObserver $observer)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getProduct();
        $this->_indexerProcessor->reindexRow($product->getEntityId());
    }
}
