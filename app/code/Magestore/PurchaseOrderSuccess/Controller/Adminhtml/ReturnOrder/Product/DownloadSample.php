<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder\Product;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Controller DownloadSample
 *
 * @SuppressWarnings(PHPMD.AllPurposeAction)
 */
class DownloadSample extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder\AbstractAction
{
    protected $csvProcessor;
    protected $fileFactory;
    protected $filesystem;
    protected $fileWriteFactory;
    protected $driverFile;

    /**
     * Execute
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->initFileVariable();

        $params = $this->getRequest()->getParams();

        $name = hash('md5', microtime());
        $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->create('import');
        $filename = DirectoryList::VAR_DIR . '/import-return-order/' . $name . '.csv';

        $stream = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->openFile($filename, 'w+');
        $stream->lock();
        $data = [
            ['PRODUCT_SKU', 'QTY_RETURNED']
        ];
        $productData = $this->returnService->generateImportData($params['return_id'], $params['supplier_id']);
        $data = array_merge($data, $productData);
        foreach ($data as $row) {
            $stream->writeCsv($row);
        }

        $stream->unlock();
        $stream->close();

        return $this->fileFactory->create(
            'import_product_to_return_order.csv',
            [
                'type' => 'filename',
                'value' => $filename,
                'rm' => true  // can delete file after use
            ],
            DirectoryList::VAR_DIR
        );
    }

    /**
     * Init File Variable
     */
    private function initFileVariable()
    {
        $this->csvProcessor = $this->_objectManager->get(\Magento\Framework\File\Csv::class);
        $this->fileFactory = $this->_objectManager->get(\Magento\Framework\App\Response\Http\FileFactory::class);
        $this->filesystem = $this->_objectManager->get(\Magento\Framework\Filesystem::class);
        $this->fileWriteFactory = $this->_objectManager->get(\Magento\Framework\Filesystem\File\WriteFactory::class);
        $this->driverFile = $this->_objectManager->get(\Magento\Framework\Filesystem\Driver\File::class);
    }
}
