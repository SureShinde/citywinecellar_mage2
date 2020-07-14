<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Ui\DataProvider\Barcode;

use Magestore\BarcodeSuccess\Ui\DataProvider\AbstractProvider;

/**
 * Class Barcode
 * @package Magestore\BarcodeSuccess\Ui\DataProvider\Barcode
 */
class DataProvider extends AbstractProvider
{
    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var string
     */
    protected $type_provider;

    /**
     * @var ProductInterface
     */
    protected $productFactory;

    /**
     * @var ImageHelper
     */
    protected $imageHelper;

    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;


    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Magento\Framework\Api\Search\ReportingInterface $reporting
     * @param \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magestore\BarcodeSuccess\Helper\Data $helper
     * @param \Magestore\BarcodeSuccess\Model\Locator\LocatorInterface $locator
     * @param \Magestore\BarcodeSuccess\Model\ResourceModel\Barcode\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\Api\Search\ReportingInterface $reporting,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magestore\BarcodeSuccess\Helper\Data $helper,
        \Magestore\BarcodeSuccess\Model\Locator\LocatorInterface $locator,
        \Magestore\BarcodeSuccess\Model\ResourceModel\Barcode\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $urlBuilder,
            $helper,
            $locator,
            $meta,
            $data
        );
        $this->collection = $collectionFactory->create();
        $this->productFactory = $productFactory;
        $this->imageHelper = $imageHelper;
        $this->stockRegistry = $stockRegistry;
        if(isset($data['type_provider']) && $data['type_provider']) {
            $this->type_provider = $data['type_provider'];
        }
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        if($this->type_provider == 'form') {
            $items = $this->collection->getItems();
            foreach ($items as $item) {
                $this->loadedData[$item->getId()] = $item->getData();
            }

            if (!empty($data)) {
                $item = $this->collection->getNewEmptyItem();
                $item->setData($data);
                $this->loadedData[$item->getId()] = $item->getData();
            }
            if($id = $item->getProductId()) {
                $product = $this->productFactory->create();
                $product->load($id);
                $stockAvailability = $this->stockRegistry->getStockStatus($id)->getStockStatus();
                $origImageHelper = $this->imageHelper->init($product, 'product_listing_thumbnail_preview');

                $this->loadedData[$item->getId()]['product_image'] = $origImageHelper->getUrl();
                $this->loadedData[$item->getId()]['product_name'] = $product->getName();
                $this->loadedData[$item->getId()]['product_price'] = $this->helper->formatPrice($product->getPrice());
                $this->loadedData[$item->getId()]['product_weight'] = $product->getWeight();
                $this->loadedData[$item->getId()]['product_color'] = $product->getColor();
                $this->loadedData[$item->getId()]['product_stock'] = ($stockAvailability)?__('In Stock'):__('Out of Stock');
                $this->loadedData[$item->getId()]['product_status'] = ($product->getStatus())?__('Enabled'):__('Disabled');
                $this->loadedData[$item->getId()]['more_detail'] = $this->urlBuilder->getUrl(
                    'catalog/product/edit',
                    ['id' => $id, 'store' => $product->getStore()->getId()]
                );
            }
            if(isset($this->loadedData[$item->getId()]['created_at'])){
                $this->loadedData[$item->getId()]['created_at'] = $this->helper->formatDate($this->loadedData[$item->getId()]['created_at']);
            }
            if(isset($this->loadedData[$item->getId()]['purchased_time'])){
                $this->loadedData[$item->getId()]['purchased_time'] = $this->helper->formatDate($this->loadedData[$item->getId()]['purchased_time']);
            }
            $this->loadedData[$item->getId()]['print_barcode'] = \Zend_Json::encode([
                'params' => ['selected' => $item->getId()],
                'url' => $this->urlBuilder->getUrl(
                    'barcodesuccess/index/printBarcode'
                )
            ]);
            $this->loadedData[$item->getId()]['type'] = $this->helper->getStoreConfig('barcodesuccess/general/default_barcode_template');
            $this->loadedData[$item->getId()]['preview'] = $this->urlBuilder->getUrl('barcodesuccess/template/preview', [
                'barcode' => $item->getBarcode(),
                'qty' => ''
            ]);
        }else{
            $this->loadedData = $this->getCollection()->toArray();
        }
        return $this->loadedData;
    }
}
