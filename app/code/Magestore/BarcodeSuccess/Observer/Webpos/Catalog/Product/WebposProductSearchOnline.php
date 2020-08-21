<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Observer\Webpos\Catalog\Product;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magestore\BarcodeSuccess\Model\ResourceModel\Barcode\CollectionFactory;

/**
 * Class WebposProductSearchOnline
 *
 * Used to observe webpos product search online
 */
class WebposProductSearchOnline implements ObserverInterface
{
    /**
     * @var CollectionFactory
     */
    protected $_barcodeCollectionFactory;

    /**
     * WebposProductSearchOnline constructor.
     *
     * @param CollectionFactory $_barcodeCollectionFactory
     */
    public function __construct(
        CollectionFactory $_barcodeCollectionFactory
    ) {
        $this->_barcodeCollectionFactory = $_barcodeCollectionFactory;
    }

    /**
     * Execute
     *
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        $barcodeString = $observer->getSearchString();
        $result = $observer->getResult();
        if ($barcodeString && $barcodeString != '' && $barcodeString != '%%') {
            $barcodes = $this->_barcodeCollectionFactory->create()
                ->addFieldToFilter('barcode', ['like' => $barcodeString]);
            $list_sku = $barcodes->getColumnValues('product_sku');
            $result->setData($list_sku);
        }
    }
}
