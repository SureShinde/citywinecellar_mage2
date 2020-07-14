<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\Source\Adminhtml\Product;

/**
 * Class Barcodeattribute
 * @package Magestore\FulfilSuccess\Model\Source\Adminhtml\Product
 */
class Barcodeattribute implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            array('value' => '', 'label' => __('-- Select Attribute --'))
        ];
        $attributes = \Magento\Framework\App\ObjectManager::getInstance()->create(
            '\Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection'
        )
            ->addFieldToFilter('is_unique', 1);
        if (!empty($attributes)) {
            foreach ($attributes as $attribute) {
                $options[] = array('value' => $attribute->getAttributeCode(), 'label' => $attribute->getFrontendLabel());
            }
        }
        return $options;
    }

}
