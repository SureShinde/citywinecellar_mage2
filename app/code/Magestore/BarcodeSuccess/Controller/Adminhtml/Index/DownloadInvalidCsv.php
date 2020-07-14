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
class DownloadInvalidCsv extends \Magestore\BarcodeSuccess\Controller\Adminhtml\AbstractIndex
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
        Csv $csvProcessor
    ) {
        parent::__construct($context, $resultPageFactory, $data, $locator);
        $this->fileFactory = $fileFactory;
        $this->filesystem = $filesystem;
        $this->csvProcessor = $csvProcessor;
    }
    public function execute()
    {
        $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->create('import');
        $filename = $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR)
                        ->getAbsolutePath('import_product_invalid.csv');
        return $this->fileFactory->create(
            'import_product_invalid.csv',
            file_get_contents($filename),
            DirectoryList::VAR_DIR
        );
    }
    
}
