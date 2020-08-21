<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Adapter\FieldMapper;

use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\AttributeProvider;
use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\FieldProviderInterface;
use Magento\Elasticsearch\Model\Adapter\FieldMapperInterface;
use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\FieldProvider\FieldName\ResolverInterface;
use Magento\Framework\App\ObjectManager;

/**
 * Product Field Mapper for pos elastic search
 *
 * Follow Magento\Elasticsearch\Elasticsearch5\Model\Adapter\FieldMapper\ProductFieldMapper
 */
class ProductFieldMapper implements FieldMapperInterface
{
    /**
     * @var AttributeProvider
     */
    private $attributeAdapterProvider;

    /**
     * @var ResolverInterface
     */
    private $fieldNameResolver;

    /**
     * @var FieldProviderInterface
     */
    private $fieldProvider;

    /**
     * ProductFieldMapper construction
     *
     * @param ResolverInterface|null $fieldNameResolver
     * @param AttributeProvider|null $attributeAdapterProvider
     * @param FieldProviderInterface|null $fieldProvider
     */
    public function __construct(
        ResolverInterface $fieldNameResolver = null,
        AttributeProvider $attributeAdapterProvider = null,
        FieldProviderInterface $fieldProvider = null
    ) {
        $this->fieldNameResolver = $fieldNameResolver ?: ObjectManager::getInstance()
            ->get(ResolverInterface::class);
        $this->attributeAdapterProvider = $attributeAdapterProvider ?: ObjectManager::getInstance()
            ->get(AttributeProvider::class);
        $this->fieldProvider = $fieldProvider ?: ObjectManager::getInstance()
            ->get(FieldProviderInterface::class);
    }

    /**
     * Get field name.
     *
     * @param string $attributeCode
     * @param array $context
     * @return string
     */
    public function getFieldName($attributeCode, $context = [])
    {
        $attributeAdapter = $this->attributeAdapterProvider->getByAttributeCode($attributeCode);
        return $this->fieldNameResolver->getFieldName($attributeAdapter, $context);
    }

    /**
     * Get all attributes types.
     *
     * @param array $context
     * @return array
     */
    public function getAllAttributesTypes($context = [])
    {
        return $this->fieldProvider->getFields($context);
    }
}
