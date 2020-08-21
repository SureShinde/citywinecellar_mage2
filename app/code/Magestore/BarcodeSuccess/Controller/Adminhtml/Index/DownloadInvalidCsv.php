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
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;

/**
 * Class Import
 *
 * Used to create download invalid csv
 */
class DownloadInvalidCsv extends \Magestore\BarcodeSuccess\Controller\Adminhtml\AbstractIndex implements
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
     * @var DriverInterface
     */
    protected $driver;

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
     * @param DriverInterface $driver
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $data,
        LocatorInterface $locator,
        FileFactory $fileFactory,
        Filesystem $filesystem,
        Csv $csvProcessor,
        DriverInterface $driver
    ) {
        parent::__construct($context, $resultPageFactory, $data, $locator);
        $this->fileFactory = $fileFactory;
        $this->filesystem = $filesystem;
        $this->csvProcessor = $csvProcessor;
        $this->driver = $driver;
    }

    /**
     * Execute function
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute()
    {
        $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->create('import');
        $filename = $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR)
                        ->getAbsolutePath('import_product_invalid.csv');
        return $this->fileFactory->create(
            'import_product_invalid.csv',
            $this->driver->fileGetContents($filename),
            DirectoryList::VAR_DIR
        );
    }
}
