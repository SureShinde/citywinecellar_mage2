<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Ui\DataProvider\PrintPage\Form;

use Magestore\BarcodeSuccess\Model\ResourceModel\Template\CollectionFactory;
use Magestore\BarcodeSuccess\Helper\Data as HelperData;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\UrlInterface;

/**
 * Class DataProvider
 * @package Magestore\BarcodeSuccess\Ui\DataProvider
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param UrlInterface $urlBuilder
     * @param CollectionFactory $collectionFactory
     * @param HelperData $helper
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        UrlInterface $urlBuilder,
        CollectionFactory $collectionFactory,
        HelperData $helper,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->urlBuilder = $urlBuilder;
        $this->helper = $helper;
    }

    public function getData()
    {
        $this->loadedData = $this->getCollection()->toArray();
        $items = $this->collection->getItems();

        if(count($items) == 0){
            $item = $this->collection->getNewEmptyItem();
            $this->loadedData[$item->getId()] = $item->getData();
            $this->loadedData[$item->getId()]['type'] = $this->helper->getStoreConfig('barcodesuccess/general/default_barcode_template');
            $this->loadedData[$item->getId()]['preview'] = $this->urlBuilder->getUrl('barcodesuccess/template/preview');
        }else{
            foreach ($items as $item) {
                $this->loadedData[$item->getId()] = $item->getData();
                $this->loadedData[$item->getId()]['preview'] = $this->urlBuilder->getUrl('barcodesuccess/template/preview');
            }
        }
        return $this->loadedData;
    }
}