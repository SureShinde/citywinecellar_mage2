<?php
/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Controller\Adminhtml\AdjustStock;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Export
 *
 * Export adjust stock controller
 */
class Export extends AdjustStock implements HttpGetActionInterface
{
    const SAMPLE_QTY = 1;

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $this->getBaseDirMedia()->create('magestore/inventory/adjuststock');
        $filename = $this->getBaseDirMedia()->getAbsolutePath('magestore/inventory/adjuststock/adjusted_products.csv');

        $data = array_merge([
            [
                __('No.'),
                __('SKU'),
                __('Product Name'),
                __('Barcode'),
                __('Old Qty'),
                __('Adjust Qty'),
                __('New Qty')
            ],
        ], $this->getProductCollection());
        $this->csvProcessor->saveData($filename, $data);
        return $this->fileFactory->create(
            'adjusted_products.csv',
            $this->driverFile->fileGetContents($filename),
            DirectoryList::VAR_DIR
        );
    }

    /**
     * Get csv url
     *
     * @return string
     */
    public function getCsvLink()
    {
        $path = 'magestore/inventory/adjuststock/adjusted_products.csv';
        $url =  $this->_url->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $path;
        return $url;
    }

    /**
     * Get base dir media
     *
     * @return \Magento\Framework\Filesystem\Directory\WriteInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getBaseDirMedia()
    {
        return $this->filesystem->getDirectoryWrite('media');
    }

    /**
     * Get adjusted product collection
     *
     * @param
     * @return array
     */
    public function getProductCollection()
    {
        $adjustStockId = $this->getRequest()->getParam('id');
        $data = [];

        if (isset($adjustStockId)) {
            $adjustStock = $this->adjustStockFactory->create();
            $adjustStock->setId($adjustStockId);
            $productCollection = $adjustStock->getProductCollection();
            $number = 1;
            foreach ($productCollection as $productModel) {
                $data[]= [
                    $number,
                    $productModel->getData('product_sku'),
                    $productModel->getData('product_name'),
                    $productModel->getData('barcode'),
                    $productModel->getData('old_qty'),
                    $productModel->getData('change_qty'),
                    $productModel->getData('new_qty'),
                ];
                $number ++;
            }
        }
        return $data;
    }
}
