<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Controller\Adminhtml\Supplier;

use Magento\Framework\App\Filesystem\DirectoryList;
use \Magestore\SupplierSuccess\Controller\Adminhtml\AbstractSupplier;

/**
 * Class DownloadSample
 * @package Magestore\SupplierSuccess\Controller\Adminhtml\Supplier
 */
class DownloadSample extends AbstractSupplier
{
    const SAMPLE_QTY = 1;
    const NUMBER_PRODUCT = 5;
    
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $name = md5(microtime());
        $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->create('import');
        $filename = DirectoryList::VAR_DIR.'/import/'.$name.'.csv';

        $stream = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->openFile($filename, 'w+');
        $stream->lock();
        $header[] = [
            __('PRODUCT_SKU'),
            __('COST'),
            __('TAX'),
            __('PRODUCT_SUPPLIER_SKU')
        ];
        $data = array_merge($header, $this->generateSampleData(self::NUMBER_PRODUCT));
        foreach ($data as $row) {
            $stream->writeCsv($row);
        }
        $stream->unlock();
        $stream->close();

        return $this->_fileFactory->create(
            'import_product_to_supplier.csv',
            array(
                'type' => 'filename',
                'value' => $filename,
                'rm' => true  // can delete file after use
            ),
            DirectoryList::VAR_DIR
        );
    }

    /**
     * get sample csv url
     *
     * @return string
     */
    public function getCsvSampleLink()
    {
        $path = 'magestore/suppliersuccess/supplier/import_product_to_supplier.csv';
        $url =  $this->_url->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $path;
        return $url;
    }

    /**
     * get base dir media
     *
     * @return string
     */
    public function getBaseDirMedia()
    {
        return $this->filesystem->getDirectoryRead('media');
    }

    /**
     * generate sample data
     *
     * @param int
     * @return array
     */
    public function generateSampleData($number)
    {
        $data = [];
        $supplierId = $this->getRequest()->getParam('id');
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
        $productCollection = $this->_objectManager->get(
            '\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory'
        )->create();
        $productCollection->addAttributeToFilter('type_id', \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
            ->addAttributeToSelect('price')
            ->setPageSize($number)
            ->setCurPage(1);
        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($productCollection as $product) {
            $data[] = [
                $product->getSku(),
                round(rand(0.5*$product->getFinalPrice(), $product->getFinalPrice()), 2),
                round(rand(0, 10), 2),
                $product->getSku()
            ];
        }

        return $data;
    }
}
