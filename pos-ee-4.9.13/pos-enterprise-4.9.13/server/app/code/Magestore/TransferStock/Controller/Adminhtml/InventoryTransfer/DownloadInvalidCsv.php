<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

/**
 * Class DownloadInvalidCsv
 * @package Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer
 */
class DownloadInvalidCsv extends \Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer\InventoryTransfer
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute()
    {
        $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->create('import');
        $filename = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->getAbsolutePath('import/import_product_to_transferstock_invalid.csv');
        return $this->fileFactory->create(
            'import_product_to_transferstock_invalid.csv',
            file_get_contents($filename),
            DirectoryList::VAR_DIR
        );
    }
}
