<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Model\Source\Adminhtml;

/**
 * Class Source
 *
 * Source model
 */
class Source implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\InventoryApi\Api\SourceRepositoryInterface
     */
    protected $sourceRepository;

    /**
     * Source constructor.
     * @param \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
     */
    public function __construct(
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
    ) {
        $this->sourceRepository = $sourceRepository;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $collection = $this->sourceRepository->getList();
        $options = [];
        $items = $collection->getItems();
        if (count($items)) {
            /** @var \Magento\InventoryApi\Api\Data\SourceInterface $source */
            foreach ($items as $source) {
                $label = $source->getName() . ' (' . $source->getSourceCode() . ') ';
                $options[] = ['value' => $source->getSourceCode(), 'label' => $label];
            }
        }
        return $options;
    }
}
