<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\Source\Adminhtml\Product;

/**
 * Source product Barcodeattribute
 */
class Barcodeattribute implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * To Option Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['value' => '', 'label' => __('-- Select Attribute --')]
        ];
        $attributes = \Magento\Framework\App\ObjectManager::getInstance()->create(
            \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection::class
        )->addFieldToFilter('is_unique', 1);
        if (!empty($attributes)) {
            foreach ($attributes as $attribute) {
                $options[] = ['value' => $attribute->getAttributeCode(), 'label' => $attribute->getFrontendLabel()];
            }
        }
        return $options;
    }
}
