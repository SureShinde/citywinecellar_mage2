<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Controller\Adminhtml\Supplier\Pricinglist;

use Magento\Framework\App\Filesystem\DirectoryList;
use \Magestore\SupplierSuccess\Controller\Adminhtml\AbstractSupplier;

/**
 * Controller DownloadSample pricelist
 *
 * @SuppressWarnings(PHPMD.AllPurposeAction)
 */
class DownloadSample extends AbstractSupplier
{
    const ADMIN_RESOURCE = 'Magestore_SupplierSuccess::supplier_pricinglist_edit';
    const SAMPLE_QTY = 1;
    const NUMBER_PRODUCT = 5;

    /**
     * Execute
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute()
    {
        $name = hash('sha256', microtime());
        $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->create('import');
        $filename = DirectoryList::VAR_DIR . '/import/' . $name . '.csv';

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
            'import_pricinglist_to_supplier.csv',
            [
                'type' => 'filename',
                'value' => $filename,
                'rm' => true  // can delete file after use
            ],
            DirectoryList::VAR_DIR
        );
    }

    /**
     * Get sample csv url
     *
     * @return string
     */
    public function getCsvSampleLink()
    {
        $path = 'magestore/suppliersuccess/supplier/import_pricinglist_to_supplier.csv';
        $url = $this->_url->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $path;
        return $url;
    }

    /**
     * Get base dir media
     *
     * @return string
     */
    public function getBaseDirMedia()
    {
        return $this->filesystem->getDirectoryRead('media');
    }

    /**
     * Generate sample data
     *
     * @param int $number
     * @return array
     */
    public function generateSampleData($number)
    {
        $data = [];
        /** @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Collection $supplierCollection */
        $supplierCollection = $this->supplierCollectionFactory->create();
        /** @var \Magestore\SupplierSuccess\Model\Supplier $supplier */
        $supplier = $supplierCollection->setPageSize(1)->setCurPage(1)->getFirstItem();
        $supplierCode = '';
        if ($supplier->getSupplierCode()) {
            $supplierCode = $supplier->getSupplierCode();
        }
        if ($supplierCode) {
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
            $productCollection = $this->_objectManager->get(
                \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory::class
            )->create();
            $productCollection->addAttributeToFilter(
                'type_id',
                \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
            )
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
                    date('Y-m-d', strtotime('now') + rand(30, 355) * 86400)
                ];
            }
        }

        return $data;
    }
}
