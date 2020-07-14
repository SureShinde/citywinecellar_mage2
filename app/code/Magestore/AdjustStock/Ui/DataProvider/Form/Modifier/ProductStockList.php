<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Ui\DataProvider\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;


class ProductStockList extends ProductDataProvider
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Product collection
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $collection;

    /**
     * @var \Magento\Ui\DataProvider\AddFieldToCollectionInterface[]
     */
    protected $addFieldStrategies;

    /**
     * @var \Magento\Ui\DataProvider\AddFilterToCollectionInterface[]
     */
    protected $addFilterStrategies;
    /**
     * @var  \Magestore\AdjustStock\Model\AdjustStockFactory
     */
    protected $adjustStockFactory;

    /**
     * ProductStockList constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param \Magestore\AdjustStock\Model\AdjustStockFactory $adjustStockFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param array $addFieldStrategies
     * @param array $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        \Magestore\AdjustStock\Model\AdjustStockFactory $adjustStockFactory,
        \Magento\Framework\App\RequestInterface $request,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $collectionFactory,
            $addFieldStrategies,
            $addFilterStrategies,
            $meta,
            $data
        );
        $this->request = $request;
        $this->adjustStockFactory = $adjustStockFactory;
        $this->collection = $this->getProductCollection();
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
        $items = $this->getCollection()->toArray();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $path = $storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        foreach ($items as &$item) {
            if(isset($item['image'])) {
                $item['image_url'] = $path.'catalog/product'.$item['image'];
            }
            if(!isset($item['new_qty'])) {
                $item['new_qty'] = 0;
            }
            if(!isset($item['stocktaking_qty'])) {
                $item['stocktaking_qty'] = 0;
            }
        }

        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getProductCollection()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $collection = $objectManager->create('Magestore\AdjustStock\Model\ResourceModel\AdjustStock\GlobalStock\Collection');

        return $collection;
    }

    public function getSourceCode() {
        $adjuststockId = $this->request->getParam('adjuststock_id');

        $adjustStock = $this->adjustStockFactory->create()->load($adjuststockId);
        $sourceCode = $adjustStock->getSourceCode();

        if($this->request->getParam('source_code')){
            $sourceCode = $this->request->getParam('source_code');
        }

        return $sourceCode;
    }

}
