<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magestore\BarcodeSuccess\Helper\Data;
use Magestore\BarcodeSuccess\Model\Locator\LocatorInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\File\Csv;
use Magento\Framework\App\Action\HttpGetActionInterface;

/**
 * Class Import
 *
 * Used to download sample
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DownloadSample extends \Magestore\BarcodeSuccess\Controller\Adminhtml\AbstractIndex implements
    HttpGetActionInterface
{
    const SAMPLE_QTY = 1;

    /**
     * @var array
     */
    protected $generated = [];

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Csv
     */
    protected $csvProcessor;

    /**
     * @var Filesystem\File\WriteFactory
     */
    protected $fileWriteFactory;

    /**
     * @var Filesystem\Driver\File
     */
    protected $driverFile;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * DownloadSample constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $data
     * @param LocatorInterface $locator
     * @param FileFactory $fileFactory
     * @param Filesystem $filesystem
     * @param Csv $csvProcessor
     * @param Filesystem\File\WriteFactory $fileWriteFactory
     * @param Filesystem\Driver\File $driverFile
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $data,
        LocatorInterface $locator,
        FileFactory $fileFactory,
        Filesystem $filesystem,
        Csv $csvProcessor,
        \Magento\Framework\Filesystem\File\WriteFactory $fileWriteFactory,
        \Magento\Framework\Filesystem\Driver\File $driverFile,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        parent::__construct($context, $resultPageFactory, $data, $locator);
        $this->fileFactory = $fileFactory;
        $this->filesystem = $filesystem;
        $this->csvProcessor = $csvProcessor;
        $this->fileWriteFactory = $fileWriteFactory;
        $this->driverFile = $driverFile;
        $this->date = $date;
    }

    /**
     * Execute
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute()
    {
        $name = sha1(microtime());
        $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->create('import');
        $filename = DirectoryList::VAR_DIR . '/import/' . $name . '.csv';

        $stream = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->openFile($filename, 'w+');
        $stream->lock();
        $data = [
            ['SKU', 'BARCODE', 'QTY', 'SUPPLIER', 'PURCHASE_TIME']
        ];
        $data = array_merge($data, $this->generateSampleData(3));
        foreach ($data as $row) {
            $stream->writeCsv($row);
        }
        $stream->unlock();
        $stream->close();

        return $this->fileFactory->create(
            'import_product_to_barcode.csv',
            [
                'type' => 'filename',
                'value' => $filename,
                'rm' => true  // can delete file after use
            ],
            DirectoryList::VAR_DIR
        );
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

        $productCollection = $this->_objectManager
            ->create(\Magento\Catalog\Model\ResourceModel\Product\Collection::class)
            ->setPageSize($number)
            ->setCurPage(1);
        foreach ($productCollection as $productModel) {
            $timeSite = date("Y-m-d H:i:s", $this->date->timestamp());
            $code = $this->generateBarcode($this->generated);
            $data[] = [$productModel->getData('sku'), $code, self::SAMPLE_QTY, '', $timeSite];
        }

        return $data;
    }

    /**
     * Generate barcode
     *
     * @param array $generated
     * @return mixed
     */
    public function generateBarcode($generated)
    {
        $code = $this->helper->generateBarcode();
        if (in_array($code, $generated)) {
            $code = $this->generateBarcode($generated);
        }
        return $code;
    }
}
