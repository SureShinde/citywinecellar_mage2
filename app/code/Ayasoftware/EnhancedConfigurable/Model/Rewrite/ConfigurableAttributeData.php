<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ayasoftware\EnhancedConfigurable\Model\Rewrite;

use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute;

class ConfigurableAttributeData extends \Magento\ConfigurableProduct\Model\ConfigurableAttributeData
{

    protected $eavConfig;

    public function __construct(\Magento\Eav\Model\Config $eavConfig) {
        $this->eavConfig = $eavConfig;
    }

    public function getAttributeOptionsData($attribute, $config) {
        $attributeOptionsData = [];

        $attributeId = $attribute->getAttributeId();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $eavModel = $objectManager->create('Magento\Catalog\Model\ResourceModel\Eav\Attribute');
        $attr = $eavModel->load($attributeId);
        $attributeCode=$eavModel->getAttributeCode();


        $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeCode);
        $options = $attribute->getSource()->getAllOptions();


        foreach ($options as $attributeOption) {
            $optionId = $attributeOption['value'];
            $attributeOptionsData[] = [
                'id' => $optionId,
                'label' => $attributeOption['label'],
                'products' => isset($config[$attribute->getAttributeId()][$optionId])
                    ? $config[$attribute->getAttributeId()][$optionId]
                    : [],
            ];
        }
        return $attributeOptionsData;
    }

}
