<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Model\Rule\Condition;

class Product extends AbstractCondition
{
    const CATALOG_PRODUCT_ENTITY_TABLE = 'amlanding_catalog_product_entity';

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory
     */
    protected $entityAttributeSetCollectionFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var string
     */
    private $productIdLink;

    /**
     * @var bool
     */
    private $requireProductEntityTable = false;

    /**
     * @var \Amasty\Xlanding\Model\Indexer\Catalog\Category\Product\TableResolver
     */
    private $tableResolver;

    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Eav\Model\Config $config,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $entityAttributeSetCollectionFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Amasty\Xlanding\Model\Indexer\Catalog\Category\Product\TableResolver $tableResolver,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $backendData,
            $config,
            $productFactory,
            $productRepository,
            $productResource,
            $attrSetCollection,
            $localeFormat,
            $data
        );

        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->entityAttributeSetCollectionFactory = $entityAttributeSetCollectionFactory;
        $this->eavConfig = $eavConfig;
        $this->productIdLink = $productMetadata->getEdition() != 'Community' ? 'row_id' : 'entity_id';
        $this->tableResolver = $tableResolver;
    }

    public function getAttributeName()
    {
        if ($this->getAttribute() === 'attribute_set_id') {
            return __('Attribute Set');
        }

        return $this->getAttributeObject()->getFrontendLabel();
    }

    protected function _prepareValueOptions()
    {
        if ($this->getAttribute() === 'attribute_set_id') {
            $entityTypeId = $this->eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY)->getId();
            $selectOptions = $this->entityAttributeSetCollectionFactory->create()
                ->setEntityTypeFilter($entityTypeId)
                ->load()
                ->toOptionArray();
            $this->setData('value_select_options', $selectOptions);
        }

        return parent::_prepareValueOptions();
    }

    public function getInputType()
    {
        if ($this->getAttribute() === 'attribute_set_id') {
            return 'select';
        }

        return parent::getInputType();
    }

    public function getValueElementType()
    {
        if ($this->getAttribute() === 'attribute_set_id') {
            return 'select';
        }

        return parent::getValueElementType();
    }

    protected function digCategories(array $ids)
    {
        $allIds = $ids;

        do {
            $categories = $this->categoryCollectionFactory->create();
            $categories->addAttributeToFilter('parent_id', ['in' => $ids]);
            $ids = $categories->getAllIds();
            $allIds = array_merge($allIds, $ids);
        } while ($ids);

        return $allIds;
    }

    public function getFilterValue()
    {
        $value = parent::getValue();
        $attribute = $this->getAttributeObject()->getAttributeCode();

        if ($attribute == 'category_ids') {
            if (!is_array($value)) {
                $value = array_map('trim', explode(',', $value));
            }

            $value = $this->digCategories($value);
        }

        if ($attribute == 'sku' && !is_array($value)) {
            $value = array_map('trim', explode(',', $value));
        }

        return $value;
    }

    protected function getMappingData()
    {
        list($alias, $table, $joinConditions, $valueField, $storeId) = $this->getPreparedMappedDataParams();

        $value = $this->getFilterValue();
        $operator = $this->getOperatorForValidate();

        if (!is_array($alias)) {
            $condition = $this->_getCondition($alias, $valueField, $operator, $value);
        } else {
            $tmp = 'tmp_xlanding_attribute';
            $eavColumn = "IFNULL(`{$alias[1]}`.`$valueField`, `{$alias[0]}`.`$valueField`)";
            $condition = $this->getOperatorCondition($tmp, $operator, $value);
            $limit = 1;
            $condition = str_replace('`' . $tmp . '`', $eavColumn, $condition, $limit);
        }

        $this->_condition = $condition;

        return [$alias, $table, $joinConditions];
    }

    /**
     * @return array
     */
    private function getPreparedMappedDataParams()
    {
        $joinConditions = '';
        $valueField = 'value';
        $storeId = 0;
        $alias = $this->_getAlias();
        $fieldToTableMap = $this->_getFieldToTableMap($alias);

        if ($fieldToTableMap) {
            list($table, $joinConditions, $valueField) = $fieldToTableMap;
            $table = $this->_productResource->getTable($table);
        } else {
            if ($this->isSelectFromIndexTable()) {
                $storeId = $this->getStoreManager()->getStore()->getId();
                $table = $this->_productResource->getTable('amasty_merchandiser_product_index_eav');
                $this->productIdLink = 'entity_id'; //there is no row_id column in this table
            } elseif ($this->getAttributeObject()->getBackendType()
                === \Magento\Eav\Model\Entity\Attribute\AbstractAttribute::TYPE_STATIC
            ) {
                $table = $this->getAttributeObject()->getBackendTable();
                $joinConditions = sprintf($this->getJoinConditionsPattern(), $alias);
                $valueField = $this->getAttributeObject()->getAttributeCode();
            } else {
                $storeId = $this->getStoreManager()->getStore()->getId();

                if ($this->getAttributeObject()->getScope()
                    === \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL_TEXT
                ) {
                    $storeId = 0;
                }

                $table = $this->getAttributeObject()->getBackendTable();
            }
        }

        if (!$joinConditions) {
            $attributeId = $this->getAttributeObject()->getId();
            $joinPattern = $this->getJoinConditionsPattern()
                . ' AND %1$s.attribute_id = %2$d AND %1$s.store_id = %3$d';
            $joinConditions = sprintf($joinPattern, $alias, $attributeId, $storeId);

            if ($storeId && !$this->isSelectFromIndexTable()) {
                $defaultAlias = 'tad_' . $alias;
                $alias = [$defaultAlias, $alias];
                $storeId = [0, $storeId];
                $joinConditions = [$joinConditions];
                array_unshift(
                    $joinConditions,
                    sprintf($joinPattern, $alias[0], $attributeId, $storeId[0])
                );
            }
        }

        return [$alias, $table, $joinConditions, $valueField, $storeId];
    }

    /**
     * @return bool
     */
    private function isSelectFromIndexTable()
    {
        return in_array($this->getAttributeObject()->getFrontendInput(), ['select', 'multiselect'], true);
    }

    /**
     * @return string
     */
    private function getJoinConditionsPattern()
    {
        if ($this->productIdLink === 'entity_id') {
            $result = 'e.entity_id = %1$s.';
        } else {
            $result = self::CATALOG_PRODUCT_ENTITY_TABLE . '.row_id = %1$s.';
            $this->requireProductEntityTable = true;
        }

        $result .= $this->productIdLink;

        return $result;
    }

    protected function _getCondition($alias, $valueField, $operator, $value)
    {
        return $this->getOperatorCondition("{$alias}.{$valueField}", $operator, $value);
    }

    protected function _getFieldToTableMap($alias)
    {
        $priceTable = $this->_productResource->getTable('catalog_product_index_price');
        $categoryTable = $this->tableResolver->getTableName($this->getStoreManager()->getStore()->getId());
        $fieldToTableMap = [
            'price' => [
                $priceTable,
                $this->_productResource->getConnection()->quoteInto(
                    'e.entity_id = ' . $alias . '.entity_id AND ' . $alias . '.website_id = ?',
                    $this->getStoreManager()->getStore()->getWebsiteId()
                ),
                'price'
            ],
            'category_ids' => [
                $categoryTable,
                'e.entity_id = ' . $alias . '.product_id',
                'category_id'
            ]
        ];

        $result = array_key_exists($this->getAttributeObject()->getAttributeCode(), $fieldToTableMap)
            ? $fieldToTableMap[$this->getAttributeObject()->getAttributeCode()]
            : null;

        return $result;
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     * @return $this
     */
    public function collectValidatedAttributes($select)
    {
        list($alias, $table, $joinConditions) = $this->getMappingData();

        if ($this->requireProductEntityTable
            && !key_exists(self::CATALOG_PRODUCT_ENTITY_TABLE, $select->getPart('from'))
        ) {
            $productEntityTable = $this->_productResource->getTable('catalog_product_entity');
            $select->join(
                [self::CATALOG_PRODUCT_ENTITY_TABLE => $productEntityTable],
                self::CATALOG_PRODUCT_ENTITY_TABLE . '.entity_id = e.entity_id',
                []
            );
        }

        if (is_array($alias) && is_array($joinConditions)) {
            $select->joinLeft([$alias[0] => $table], $joinConditions[0], []);
            $select->joinLeft([$alias[1] => $table], $joinConditions[1], []);
        } else {
            // for conditions with any aggregator inner join work as AND operator
            $joinType = 'joinLeft';
            $select->{$joinType}([$alias => $table], $joinConditions, []);
        }

        return $this;
    }

    protected function _getAttributeCode()
    {
        return $this->getAttributeObject()->getAttributeCode();
    }
}
