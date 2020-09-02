<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Model\Source\Adminhtml;

/**
 * Class Batch
 * @package Magestore\OrderSuccess\Model\Source\Adminhtml
 */
class Batch implements \Magestore\OrderSuccess\Api\Data\BatchSourceInterface
{
    /**
     * @var \Magestore\OrderSuccess\Model\ResourceModel\Batch\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Batch constructor.
     * @param \Magestore\OrderSuccess\Model\ResourceModel\Batch\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magestore\OrderSuccess\Model\ResourceModel\Batch\CollectionFactory $collectionFactory
    )
    {
        $this->collectionFactory = $collectionFactory;
    }
    /**
     * @return array
     */
    public function getBatchList()
    {
        $tagArray = $this->collectionFactory->create();
        return $tagArray;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $batchs = [];
        $batchs[] = ['value' => 'na', 'label' => __('-- Select --')];
        $batchs[] = ['value' => 'remove', 'label' => __('Remove from Batch')];
        $batchs[] = ['value' => 'newbatch', 'label' => __('Add to New Batch')];
        $batchs[] = ['value' => '0', 'label' => __('No Batch')];
        foreach($this->getBatchList() as $batch){
            $batchs[] = ['value' => $batch->getBatchId(), 'label' => $batch->getCode()];
        }
        return $batchs;
    }

    /**
     * @return array
     */
    public function getOptionArray()
    {
        $tags = [];
        foreach($this->getBatchList() as $batch){
            $tags[$batch->getBatchId()] = $batch->getCode();
        }
        return $tags;
    }

}