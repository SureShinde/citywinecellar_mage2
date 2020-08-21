<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer\InventoryTransfer;

/**
 * Inventory tranfer - Download invalid csv
 */
class DownloadInvalidCsv extends InventoryTransfer implements HttpGetActionInterface
{
    /**
     * @var \Magento\Framework\Filesystem\DriverInterface
     */
    protected $driver;

    /**
     * DownloadInvalidCsv constructor.
     *
     * @param \Magestore\TransferStock\Controller\Adminhtml\Context $context
     * @param \Magento\Framework\Filesystem\DriverInterface $driver
     */
    public function __construct(
        \Magestore\TransferStock\Controller\Adminhtml\Context $context,
        \Magento\Framework\Filesystem\DriverInterface $driver
    ) {
        parent::__construct($context);
        $this->driver = $driver;
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute()
    {
        $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->create('import');
        $filename = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)
            ->getAbsolutePath('import/import_product_to_transferstock_invalid.csv');
        return $this->fileFactory->create(
            'import_product_to_transferstock_invalid.csv',
            $this->driver->fileGetContents($filename),
            DirectoryList::VAR_DIR
        );
    }
}
