<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\PickRequest\Batch;

use Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\Batch\CollectionFactory;

class Options implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var null|array
     */
    protected $options = [];

    /**
     *
     * @var CollectionFactory
     */
    protected $collectionFactory;
    
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array|null
     */
    public function toOptionArray()
    {
        $collection = $this->collectionFactory->create();
        $this->options[] = ['value' => '0', 'label' => __('No Batch')];
        if($collection->getSize()) {
            foreach($collection->getItems() as $batch){
                $this->options[] = ['value' => $batch->getId(), 'label' => $batch->getCode()];
            }
        }
        
        return $this->options;
    }
}