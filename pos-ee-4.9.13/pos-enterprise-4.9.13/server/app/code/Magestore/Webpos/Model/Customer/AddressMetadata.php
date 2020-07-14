<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Customer;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Framework\Exception\NoSuchEntityException;
/**
 * Service to fetch customer address related custom attributes
 */
class AddressMetadata extends \Magento\Customer\Model\Metadata\AddressMetadata
{
    /**
     * {@inheritdoc}
     */
    public function getAttributes($formCode)
    {
        $object = \Magento\Framework\App\ObjectManager::getInstance();
        $attributes = [];
        $attributesFormCollection = $object->get('Magento\Customer\Model\AttributeMetadataDataProvider')->loadAttributesCollection(
            AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
            $formCode
        );
        foreach ($attributesFormCollection as $attribute) {
            $attributes[$attribute->getAttributeCode()] = $object->get('Magestore\Webpos\Model\Customer\AttributeMetadataConverter')
                ->createMetadataAttribute($attribute);
        }
        if (empty($attributes)) {
            throw NoSuchEntityException::singleField('formCode', $formCode);
        }
        return $attributes;
    }

}
