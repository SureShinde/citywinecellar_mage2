<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Ui\DataProvider\Product;

use Magento\GroupedProduct\Ui\DataProvider\Product\GroupedProductDataProvider;

class BarcodeProductDataProvider extends GroupedProductDataProvider
{
    /**
     * @var \Magestore\BarcodeSuccess\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magestore\BarcodeSuccess\Model\ResourceModel\Barcode\Collection
     */
    protected $barcodeCollection;

    /**
     * BarcodeProductDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $config
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     * @param \Magestore\BarcodeSuccess\Helper\Data $helper
     * @param \Magestore\BarcodeSuccess\Model\ResourceModel\Barcode\Collection $barcodeCollection
     * @param array $meta
     * @param array $data
     * @param array $addFieldStrategies
     * @param array $addFilterStrategies
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $config,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magestore\BarcodeSuccess\Helper\Data $helper,
        \Magestore\BarcodeSuccess\Model\ResourceModel\Barcode\Collection $barcodeCollection,
        array $meta = [],
        array $data = [],
        array $addFieldStrategies = [],
        array $addFilterStrategies = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $collectionFactory,
            $request,
            $config,
            $storeRepository,
            $meta,
            $data,
            $addFieldStrategies,
            $addFilterStrategies
        );
        $this->helper = $helper;
        $this->barcodeCollection = $barcodeCollection;

        $one_barcode_per_sku = $this->helper->getStoreConfig('barcodesuccess/general/one_barcode_per_sku');
        if($one_barcode_per_sku){
            $ignoreIds = $this->barcodeCollection->getAllProductIds();
            if(!empty($ignoreIds)){
                $this->getCollection()->addAttributeToFilter(
                    'entity_id',
                    ['nin' => $ignoreIds]
                );
            }
        }
    }

    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $composableTypes = $this->config->getComposableTypes();
            $composableTypes['giftvoucher'] = 'giftvoucher';
            $this->getCollection()->addAttributeToFilter(
                'type_id',
                $composableTypes
            );
            if ($storeId = $this->request->getParam('current_store_id')) {
                /** @var StoreInterface $store */
                $store = $this->storeRepository->getById($storeId);
                $this->getCollection()->setStore($store);
            }
            $this->getCollection()->load();
        }
        $items = $this->getCollection()->toArray();

        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items),
        ];
    }
}
