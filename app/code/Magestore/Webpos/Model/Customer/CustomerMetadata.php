<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Customer;

use Magento\Framework\Exception\NoSuchEntityException;

class CustomerMetadata extends \Magento\Customer\Model\Metadata\CustomerMetadata
{
    const IGNORE_ATTRIBUTE = [
        'created_in',
        'created_at',
        'website_id'
    ];
    /**
     * {@inheritdoc}
     */
    public function getAttributes($formCode)
    {
        $object = \Magento\Framework\App\ObjectManager::getInstance();
        $attributes = [];
        $attributesFormCollection = $object->get('Magento\Customer\Model\AttributeMetadataDataProvider')->loadAttributesCollection(
            self::ENTITY_TYPE_CUSTOMER,
            $formCode
        );
        foreach ($attributesFormCollection as $attribute) {
            if (!in_array($attribute->getAttributeCode(), self::IGNORE_ATTRIBUTE)){
                /** @var $attribute \Magento\Customer\Model\Attribute */
                $attributes[$attribute->getAttributeCode()] = $object->get('Magestore\Webpos\Model\Customer\AttributeMetadataConverter')
                    ->createMetadataAttribute($attribute);
            }
        }
        if (empty($attributes)) {
            throw NoSuchEntityException::singleField('formCode', $formCode);
        }
        return $attributes;
    }

}
