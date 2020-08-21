<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder\Product;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Controller DownloadSample
 *
 * @SuppressWarnings(PHPMD.AllPurposeAction)
 */
class DownloadSample extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\AbstractAction
{
    protected $csvProcessor;
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;
    /**
     * @var \Magento\Framework\Filesystem\File\WriteFactory
     */
    protected $fileWriteFactory;
    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $driverFile;
    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\PurchaseOrderService
     */
    protected $purchaseOrderService;

    /**
     * DownloadSample constructor.
     *
     * @param \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Context $context
     * @param \Magento\Framework\File\Csv $csvProcessor
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\File\WriteFactory $fileWriteFactory
     * @param \Magento\Framework\Filesystem\Driver\File $driverFile
     * @param \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\PurchaseOrderService $purchaseOrderService
     */
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Context $context,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\File\WriteFactory $fileWriteFactory,
        \Magento\Framework\Filesystem\Driver\File $driverFile,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\PurchaseOrderService $purchaseOrderService
    ) {
        parent::__construct($context);
        $this->csvProcessor = $csvProcessor;
        $this->fileFactory = $fileFactory;
        $this->filesystem = $filesystem;
        $this->fileWriteFactory = $fileWriteFactory;
        $this->driverFile = $driverFile;
        $this->purchaseOrderService = $purchaseOrderService;
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        $name = hash('md5', microtime());
        $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->create('import');
        $filename = DirectoryList::VAR_DIR . '/import/' . $name . '.csv';

        $stream = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->openFile($filename, 'w+');
        $stream->lock();
        $data = [
            ['PRODUCT_SKU', 'COST', "TAX", 'DISCOUNT', 'QTY_ORDERRED']
        ];
        $productData = $this->purchaseOrderService->generateImportData($params['purchase_id'], $params['supplier_id']);
        $data = array_merge($data, $productData);
        foreach ($data as $row) {
            $stream->writeCsv($row);
        }

        $stream->unlock();
        $stream->close();

        return $this->fileFactory->create(
            'import_product_to_purchase_order.csv',
            [
                'type' => 'filename',
                'value' => $filename,
                'rm' => true  // can delete file after use
            ],
            DirectoryList::VAR_DIR
        );
    }
}
