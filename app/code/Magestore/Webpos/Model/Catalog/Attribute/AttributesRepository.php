<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Catalog\Attribute;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class BundleOptionsBuilder
 * @package Magestore\Webpos\Model\Catalog\Product
 */
class AttributesRepository implements \Magestore\Webpos\Api\Catalog\Attribute\AttributesRepositoryInterface
{
    protected $defaultCustomSaleAttributes = [
        'type_id',
        'store_id',
        'sku',
        'name',
        'weight',
        'status',
        'visibility',
        'price',
        'description',
        'short_description'
    ];
    /**
     * @var \Magento\Eav\Api\AttributeSetRepositoryInterface
     */
    protected $setRepository;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    private $attributeCollectionFactory;

    /**
     * AttributesRepository constructor.
     * @param \Magento\Eav\Api\AttributeSetRepositoryInterface $setRepository
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory
     */
    public function __construct(
        \Magento\Eav\Api\AttributeSetRepositoryInterface $setRepository,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory
    ) {
        $this->setRepository = $setRepository;
        $this->eavConfig = $eavConfig;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function getCustomSaleRequireAttributes($attributeSetId) {
        /** @var \Magento\Eav\Api\Data\AttributeSetInterface $attributeSet */
        $attributeSet = $this->setRepository->get($attributeSetId);
        $requiredEntityTypeId = $this->eavConfig
            ->getEntityType(\Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE)
            ->getId();
        if (!$attributeSet->getAttributeSetId() || $attributeSet->getEntityTypeId() != $requiredEntityTypeId) {
            throw NoSuchEntityException::singleField('attributeSetId', $attributeSetId);
        }
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $attributeCollection */
        $attributeCollection = $this->attributeCollectionFactory->create();
        $attributeCollection->setAttributeSetFilter($attributeSet->getAttributeSetId());
        $attributeCollection->addFieldToFilter('is_required', 1);
        $attributeCollection->addFieldToFilter('attribute_code', ['nin' => $this->defaultCustomSaleAttributes]);
        $attributeCollection->load();

        return $attributeCollection->getItems();
    }
}