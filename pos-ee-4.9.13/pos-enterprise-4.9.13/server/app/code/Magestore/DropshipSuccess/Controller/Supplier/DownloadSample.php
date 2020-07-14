<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Controller\Supplier;

use Magento\Framework\App\Filesystem\DirectoryList;

class DownloadSample extends \Magestore\DropshipSuccess\Controller\AbstractSupplier
{

    const SAMPLE_QTY = 1;
    const NUMBER_PRODUCT = 5;

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->checkLogin();
        $name = md5(microtime());
        $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->create('import');
        $filename = DirectoryList::VAR_DIR.'/import/'.$name.'.csv';

        $stream = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->openFile($filename, 'w+');
        $stream->lock();
        $header[] = [
            __('SUPPLIER_CODE'),
            __('PRODUCT_SKU'),
            __('PRODUCT_SUPPLIER_SKU'),
            __('MINIMAL_QTY'),
            __('COST'),
            __('START_DATE'),
            __('END_DATE')
        ];
        $data = array_merge($header, $this->generateSampleData(self::NUMBER_PRODUCT));
        foreach ($data as $row) {
            $stream->writeCsv($row);
        }
        $stream->unlock();
        $stream->close();

        return $this->_fileFactory->create(
            'send_pricinglist_to_store_owner.csv',
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
        $path = 'magestore/suppliersuccess/supplier/send_pricinglist_to_store_owner.csv';
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
        /** @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Collection $supplierCollection */
        $supplierCollection = $this->supplierCollectionFactory->create();
        /** @var \Magestore\SupplierSuccess\Model\Supplier $supplier */
        $supplier = $this->supplierSession->getSupplier();
        $supplierCode = '';
        if ($supplier->getSupplierCode())
            $supplierCode = $supplier->getSupplierCode();
        if ($supplierCode) {
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
                    $supplierCode,
                    $product->getSku(),
                    $product->getSku(),
                    rand(10, 1000),
                    round(rand(0.5 * $product->getFinalPrice(), $product->getFinalPrice()), 2),
                    date('Y-m-d'),
                    date('Y-m-d', strtotime('now') + rand(30, 355)*86400)
                ];
            }
        }

        return $data;
    }
}
