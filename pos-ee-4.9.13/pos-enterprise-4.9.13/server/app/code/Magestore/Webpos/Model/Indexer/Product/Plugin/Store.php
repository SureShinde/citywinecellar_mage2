<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Indexer\Product\Plugin;

class Store
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
     * Before save handler
     *
     * @param \Magento\Store\Model\ResourceModel\Store $subject
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave(\Magento\Store\Model\ResourceModel\Store $subject, \Magento\Framework\Model\AbstractModel $object)
    {
        if (!$object->getId() || $object->dataHasChangedFor('group_id')) {
            $this->_indexerProcessor->markIndexerAsInvalid();
        }
    }
}
