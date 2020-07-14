<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Model\System\Config\Source;
/**
 * Class AttributeCode
 * @package Magestore\Webpos\Model\System\Config\Source
 */
class AttributeCode
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
     */
    protected $collection;

    /**
     * AttributeCode constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $collection
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $collection
    ){
        $this->collection = $collection;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->collection->addFieldToFilter('is_unique', 1);
        $array = [];
        $array[] = ['value' => '', 'label' => '---Select---'];
        foreach ($collection as $value) {
            $array[] = ['value' => $value->getAttributeCode(), 'label' => $value->getFrontendLabel()];
        }
        return $array;
    }
}