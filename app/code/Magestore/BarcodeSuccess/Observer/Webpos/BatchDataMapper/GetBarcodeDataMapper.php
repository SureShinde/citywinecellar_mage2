<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Observer\Webpos\BatchDataMapper;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magestore\BarcodeSuccess\Model\ResourceModel\Barcode\CollectionFactory;

/**
 * GetBarcodeDataMapper to get barcode data for elastic search indexer
 */
class GetBarcodeDataMapper implements ObserverInterface
{
    /**
     * @var CollectionFactory
     */
    protected $barcodeCollectionFactory;

    /**
     * GetBarcodeDataMapper constructor.
     *
     * @param CollectionFactory $barcodeCollectionFactory
     */
    public function __construct(
        CollectionFactory $barcodeCollectionFactory
    ) {
        $this->barcodeCollectionFactory = $barcodeCollectionFactory;
    }

    /**
     * Execute
     *
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        $productIds = $observer->getData('product_ids');
        $dataObject = $observer->getData('data_object');
        $fields = $dataObject->getData('fields');
        /** @var \Magestore\BarcodeSuccess\Model\ResourceModel\Barcode\Collection $barcodeCollection */
        $barcodeCollection = $this->barcodeCollectionFactory->create()->addFieldToFilter(
            'product_id',
            ['in' => $productIds]
        );
        /** @var \Magestore\BarcodeSuccess\Model\Barcode $barcode */
        foreach ($barcodeCollection->getItems() as $barcode) {
            $fields[$barcode->getProductId()]['barcode'][] = $barcode->getBarcode();
        }
        $dataObject->setData('fields', $fields);
        $observer->setData('data_object', $dataObject);
    }
}
