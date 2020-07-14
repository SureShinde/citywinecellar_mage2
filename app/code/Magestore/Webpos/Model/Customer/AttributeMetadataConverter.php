<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Customer;

class AttributeMetadataConverter extends \Magento\Customer\Model\AttributeMetadataConverter
{
    const IGNORE_OPTION_FROM_ATTRIBUTE = [
        'country_id',
        'region'
    ];
    /**
     * @param \Magento\Customer\Model\Attribute $attribute
     * @return \Magento\Customer\Api\Data\AttributeMetadataInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createMetadataAttribute($attribute)
    {
        $object = \Magento\Framework\App\ObjectManager::getInstance();
        $options = [];
        if ($attribute->usesSource() && !in_array($attribute->getAttributeCode(), self::IGNORE_OPTION_FROM_ATTRIBUTE)) {
            foreach ($attribute->getSource()->getAllOptions() as $option) {
                $optionDataObject = $object->create('Magento\Customer\Api\Data\OptionInterface');
                if (!is_array($option['value'])) {
                    $optionDataObject->setValue($option['value']);
                } else {
                    $optionArray = [];
                    foreach ($option['value'] as $optionArrayValues) {
                        $optionObject = $object->create('Magento\Customer\Api\Data\OptionInterface');
                        $this->dataObjectHelper->populateWithArray(
                            $optionObject,
                            $optionArrayValues,
                            \Magento\Customer\Api\Data\OptionInterface::class
                        );
                        $optionArray[] = $optionObject;
                    }
                    $optionDataObject->setOptions($optionArray);
                }
                $optionDataObject->setLabel($option['label']);
                $options[] = $optionDataObject;
            }
        }
        $validationRules = [];
        foreach ((array)$attribute->getValidateRules() as $name => $value) {
            $validationRule = $object->create('Magento\Customer\Api\Data\ValidationRuleInterface')
                ->setName($name)
                ->setValue($value);
            $validationRules[] = $validationRule;
        }

        $attributeMetaData = $object->create('Magento\Customer\Api\Data\AttributeMetadataInterface');

        if ($attributeMetaData instanceof \Magento\Eav\Api\Data\AttributeDefaultValueInterface) {
            $attributeMetaData->setDefaultValue($attribute->getDefaultValue());
        }


        $attributeData = $attributeMetaData->setAttributeCode($attribute->getAttributeCode())
            ->setFrontendInput($attribute->getFrontendInput())
            ->setInputFilter((string)$attribute->getInputFilter())
            ->setStoreLabel($attribute->getStoreLabel())
            ->setValidationRules($validationRules)
            ->setIsVisible((boolean)$attribute->getIsVisible())
            ->setIsRequired((boolean)$attribute->getIsRequired())
            ->setMultilineCount((int)$attribute->getMultilineCount())
            ->setDataModel((string)$attribute->getDataModel())
            ->setOptions($options)
            ->setFrontendClass($attribute->getFrontend()->getClass())
            ->setFrontendLabel($attribute->getFrontendLabel())
            ->setNote((string)$attribute->getNote())
            ->setIsSystem((boolean)$attribute->getIsSystem())
            ->setIsUserDefined((boolean)$attribute->getIsUserDefined())
            ->setBackendType($attribute->getBackendType())
            ->setSortOrder((int)$attribute->getSortOrder())
            ->setIsUsedInGrid($attribute->getIsUsedInGrid())
            ->setIsVisibleInGrid($attribute->getIsVisibleInGrid())
            ->setIsFilterableInGrid($attribute->getIsFilterableInGrid())
            ->setIsSearchableInGrid($attribute->getIsSearchableInGrid());

        if ($attribute->getAttributeCode() == 'prefix') {
            $optionPrefix = $object->get('Magento\Framework\App\Config\ScopeConfigInterface')
                ->getValue('customer/address/prefix_options');
            if ($optionPrefix){
                $optionArray = explode(';', $optionPrefix);
                foreach ($optionArray as $option) {
                    $options[] = [
                        'value' => $option,
                        'label' => $option
                    ];
                }
                $attributeData->setFrontendInput('select');
            }
            $attributeData->setOptions($options);
        }
        if ($attribute->getAttributeCode() == 'suffix') {
            $optionSuffix = $object->get('Magento\Framework\App\Config\ScopeConfigInterface')
                ->getValue('customer/address/suffix_options');

            if ($optionSuffix) {
                $optionArray = explode(';', $optionSuffix);
                foreach ($optionArray as $option) {
                    $options[] = [
                        'value' => $option,
                        'label' => $option
                    ];
                }
                $attributeData->setFrontendInput('select');
            }
            $attributeData->setOptions($options);
        }

        return $attributeData;
    }
}
