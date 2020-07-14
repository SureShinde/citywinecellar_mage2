<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Controller\Adminhtml\Inventory;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Download
 * @package Magestore\ReportSuccess\Controller\Adminhtml\Inventory
 */
class Download extends \Magestore\ReportSuccess\Controller\Adminhtml\Inventory
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /** @var \Magento\Framework\Filesystem\Directory\WriteInterface */
    protected $varDirectory;

    /**
     * @var \Magento\Framework\Archive
     */
    protected $archive;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;
    /**
     * @var
     */
    protected $dateTime;

    /**
     * Download constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\Archive $archive
     * @param \Magento\Framework\Filesystem $filesystem
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Archive $archive,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    ) {
        parent::__construct(
            $context, $resultPageFactory, $resultJsonFactory, $resultLayoutFactory, $resultForwardFactory
        );
        $this->fileFactory = $fileFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->archive = $archive;
        $this->localeDate = $localeDate;
        $this->dateTime = $dateTime;
        $this->varDirectory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $fileName = $this->getRequest()->getParam('name');
        $locationCode = $this->getRequest()->getParam('display_name');
        $dateObject = $this->getRequest()->getParam('date_object');

        if (!$this->reportFileExists($fileName)) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/inventory/historicalStock');
            return $resultRedirect;
        }
        $newFileName = $locationCode.'_'.$dateObject.'.tgz';
        $this->varDirectory->copyFile('historical_stock/'.$fileName, $newFileName);
        $this->fileFactory->create(
            $newFileName,
            [
                'type' => 'filename',
                'value' => $newFileName,
                'rm' => true  // can delete file after use
            ],
            DirectoryList::VAR_DIR
        );
    }


    /**
     * Get file path.
     *
     * @param string $filename
     * @return string
     */
    public function getFilePath($filename)
    {
        return $this->varDirectory->getRelativePath('historical_stock/' . $filename);
    }

    /**
     * @param $filename
     * @return bool
     *
     */
    public function reportFileExists($filename)
    {
        return $this->varDirectory->isFile($this->getFilePath($filename));
    }

    /**
     * @param $filename
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getReportOutput($filename)
    {
        return $this->varDirectory->readFile($this->getFilePath($filename));
    }

    /**
     * Retrieve report file size
     *
     * @param string $filename
     * @return int|mixed
     */
    public function getReportSize($filename)
    {
        return $this->varDirectory->stat($this->getFilePath($filename))['size'];
    }

}