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

/**
 * Class Import
 * @package Magestore\BarcodeSuccess\Controller\Adminhtml\Index
 */
class DownloadSample extends \Magestore\BarcodeSuccess\Controller\Adminhtml\AbstractIndex
{
    const SAMPLE_QTY = 1;

    /**
     * @var array
     */
    protected $generated = array();

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

    protected $fileWriteFactory;

    protected $driverFile;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * DownloadSample constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $data
     * @param LocatorInterface $locator
     * @param FileFactory $fileFactory
     * @param Filesystem $filesystem
     * @param Csv $csvProcessor
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
     * @return mixed
     */
    public function execute()
    {

        $name = md5(microtime());
        $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->create('import');
        $filename = DirectoryList::VAR_DIR.'/import/'.$name.'.csv';

        $stream = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->openFile($filename, 'w+');
        $stream->lock();
        $data = array(
            array('SKU', 'BARCODE', 'QTY', 'SUPPLIER', 'PURCHASE_TIME')
        );
        $data = array_merge($data, $this->generateSampleData(3));
        foreach ($data as $row) {
            $stream->writeCsv($row);
        }
        $stream->unlock();
        $stream->close();


        return $this->fileFactory->create(
            'import_product_to_barcode.csv',
            array(
                'type' => 'filename',
                'value' => $filename,
                'rm' => true  // can delete file after use
            ),
            DirectoryList::VAR_DIR
        );
    }

    /**
     * @param $number
     * @return array
     */
    public function generateSampleData($number) {
        $data = array();

        $productCollection = $this->_objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection')
            ->setPageSize($number)
            ->setCurPage(1);
        foreach ($productCollection as $productModel) {
            $timeSite = date("Y-m-d H:i:s", $this->date->timestamp());
            $code =  $this->generateBarcode($this->generated);
            $generated[] =  $code;
            $data[]= array($productModel->getData('sku'), $code, self::SAMPLE_QTY, '', $timeSite);
        }

        return $data;
    }

    /**
     * @param $generated
     * @return mixed
     */
    public function generateBarcode($generated){
        $code = $this->helper->generateBarcode();
        if (in_array($code, $generated)) {
            $code = $this->generateBarcode($generated);
        }
        return $code;
    }
}
