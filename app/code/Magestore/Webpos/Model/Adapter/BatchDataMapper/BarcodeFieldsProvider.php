<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Adapter\BatchDataMapper;

use Magento\Elasticsearch\Model\ResourceModel\Index;
use Magento\Store\Model\StoreManagerInterface;
use Magento\AdvancedSearch\Model\Adapter\DataMapper\AdditionalFieldsProviderInterface;
use Magento\CatalogSearch\Model\Indexer\Fulltext\Action\DataProvider;
use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\AttributeProvider;
use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\FieldProvider\FieldName\ResolverInterface;
use Magestore\Webpos\Helper\Data as PosHelper;
use Magento\Framework\Event\ManagerInterface;

/**
 * Provide data mapping for price fields
 */
class BarcodeFieldsProvider implements AdditionalFieldsProviderInterface
{
    /**
     * @var Index
     */
    private $resourceIndex;

    /**
     * @var DataProvider
     */
    private $dataProvider;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var AttributeProvider
     */
    private $attributeAdapterProvider;

    /**
     * @var ResolverInterface
     */
    private $fieldNameResolver;
    /**
     * @var PosHelper
     */
    private $posHelper;
    /**
     * @var ManagerInterface
     */
    private $eventManger;

    /**
     * BarcodeFieldsProvider constructor.
     *
     * @param Index $resourceIndex
     * @param DataProvider $dataProvider
     * @param StoreManagerInterface $storeManager
     * @param AttributeProvider $attributeAdapterProvider
     * @param ResolverInterface $fieldNameResolver
     * @param PosHelper $posHelper
     * @param ManagerInterface $eventManger
     */
    public function __construct(
        Index $resourceIndex,
        DataProvider $dataProvider,
        StoreManagerInterface $storeManager,
        AttributeProvider $attributeAdapterProvider,
        ResolverInterface $fieldNameResolver,
        PosHelper $posHelper,
        ManagerInterface $eventManger
    ) {
        $this->resourceIndex = $resourceIndex;
        $this->dataProvider = $dataProvider;
        $this->storeManager = $storeManager;
        $this->attributeAdapterProvider = $attributeAdapterProvider;
        $this->fieldNameResolver = $fieldNameResolver;
        $this->posHelper = $posHelper;
        $this->eventManger = $eventManger;
    }

    /**
     * @inheritdoc
     */
    public function getFields(array $productIds, $storeId)
    {
        $fields = [];
        if ($this->posHelper->isEnabledBarcodeManagement()) {
            $dataObject = new \Magento\Framework\DataObject(['fields' => $fields]);
            $this->eventManger->dispatch(
                'get_batch_data_mapper_barcode',
                ['data_object' => $dataObject, 'product_ids' => $productIds]
            );
            $fields = $dataObject->getData('fields');
        } else {
            $barcodeAttributeCode = $this->posHelper->getStoreConfig('webpos/product_search/barcode');
            if ($barcodeAttributeCode
                && $barcodeAttribute = $this->dataProvider->getSearchableAttribute($barcodeAttributeCode)) {
                if ($barcodeAttribute->getBackendType() == 'static') {
                    $products = $this->dataProvider->getSearchableProducts(
                        $storeId,
                        [$barcodeAttribute->getAttributeCode()],
                        $productIds,
                        0,
                        \Magestore\Webpos\Model\Indexer\IndexerHandler::DEFAULT_BATCH_SIZE
                    );
                    foreach ($products as $productData) {
                        $fields[$productData['entity_id']]['barcode'][]
                            = $productData[$barcodeAttribute->getAttributeCode()];
                    }
                } else {
                    $attributeTypes[$barcodeAttribute->getBackendType()] = [$barcodeAttribute->getAttributeId()];
                    $products = $this->dataProvider->getProductAttributes($storeId, $productIds, $attributeTypes);
                    foreach ($products as $productId => $productData) {
                        $fields[$productId]['barcode'][]
                            = $productData[$barcodeAttribute->getAttributeId()];
                    }
                }
            }
        }
        return $fields;
    }
}
