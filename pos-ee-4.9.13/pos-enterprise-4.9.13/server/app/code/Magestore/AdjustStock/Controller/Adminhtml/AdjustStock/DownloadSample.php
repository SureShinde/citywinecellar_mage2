<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Controller\Adminhtml\AdjustStock;
use Magento\Framework\App\Filesystem\DirectoryList;
/**
 * Class Import
 * @package Magestore\InventorySuccess\Controller\Adminhtml\AdjustStock
 */
class DownloadSample extends \Magestore\AdjustStock\Controller\Adminhtml\AdjustStock\AdjustStock
{
    const SAMPLE_QTY = 1;
    
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
        $qtyLabel = __('Adjust Qty');
        $skuLabel = __('SKU');
        $data = array(
            array($skuLabel,$qtyLabel)
        );
        $data = array_merge($data, $this->generateSampleData(3));
        foreach ($data as $row) {
            $stream->writeCsv($row);
        }
        $stream->unlock();
        $stream->close();

        return $this->fileFactory->create(
            'import_product_to_adjuststock.csv',
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
        $path = 'magestore/inventory/adjuststock/import_product_to_adjuststock.csv';
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
        $productCollection = $this->_objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection')
            ->setPageSize($number)
            ->setCurPage(1);
        foreach ($productCollection as $productModel) {
            $data[]= array($productModel->getData('sku'), self::SAMPLE_QTY);
        }
        return $data;
    }
}
