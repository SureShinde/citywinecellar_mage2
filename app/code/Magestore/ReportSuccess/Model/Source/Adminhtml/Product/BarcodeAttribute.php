<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\Source\Adminhtml\Product;

/**
 * Product barcode attribute model
 */
class BarcodeAttribute implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * BarcodeAttribute constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $options = [];
        $attributes = $this->collectionFactory->create()->addFieldToFilter('is_unique', 1);
        foreach ($attributes as $attribute) {
            $options[] = ['value' => $attribute->getAttributeCode(), 'label' => $attribute->getFrontendLabel()];
        }
        return $options;
    }
}
