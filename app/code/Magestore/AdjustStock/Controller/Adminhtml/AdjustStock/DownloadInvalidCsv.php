<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Controller\Adminhtml\AdjustStock;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

/**
 * Class DownloadInvalidCsv
 *
 * Used to download invalid csv file
 */
class DownloadInvalidCsv extends \Magestore\AdjustStock\Controller\Adminhtml\AdjustStock\AdjustStock implements
    HttpGetActionInterface
{
    /**
     * Execute download invalid csv
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute()
    {
        $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->create('import');
        $filename = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)
            ->getAbsolutePath('import/import_product_to_adjuststock_invalid.csv');
        return $this->fileFactory->create(
            'import_product_to_adjuststock_invalid.csv',
            $this->driverFile->fileGetContents($filename),
            DirectoryList::VAR_DIR
        );
    }

    /**
     * Get Csv sample link
     *
     * @return string
     */
    public function getCsvSampleLink()
    {
        $path = 'magestore/inventory/adjuststock/import_product_to_adjuststock_invalid.csv';
        $url = $this->_url->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $path;
        return $url;
    }

    /**
     * Get base dir media
     *
     * @return mixed
     */
    public function getBaseDirMedia()
    {
        return $this->filesystem->getDirectoryRead('media');
    }
}
