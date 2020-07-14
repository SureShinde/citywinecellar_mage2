<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magestore\BarcodeSuccess\Helper\Data;
use Magestore\BarcodeSuccess\Model\Locator\LocatorInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\File\Csv;


class ImportData extends \Magestore\BarcodeSuccess\Controller\Adminhtml\AbstractIndex
{
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
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

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
        $this->backendSession = $context->getSession();
    }

    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if ($this->getRequest()->isPost()) {
            $reason = $this->getRequest()->getParam('reason');
            try {
                $importHandler = $this->_objectManager->create('Magestore\BarcodeSuccess\Model\CsvImportHandler');
                $result = $importHandler->importFromCsvFile($this->getRequest()->getFiles('file_csv'), $reason);
                $importSuccess = $result['import_success'];
                $editSuccess = $result['edit_success'];
                if ($importSuccess) {
                    $this->messageManager->addSuccessMessage(__('%1 barcode(s) has been imported.', $importSuccess));
                    if($editSuccess)
                        $this->messageManager->addSuccessMessage(__('%1 barcode(s) has been edited.', $editSuccess));
                    return $resultRedirect->setPath("*/*/importsuccess/id/".$result['history_id']);
                } else {
                    if($editSuccess){
                        $this->messageManager->addSuccessMessage(__('%1 barcode(s) has been edited.', $editSuccess));
                        return $resultRedirect->setPath('*/*/index');
                    } else{
                        $this->messageManager->addErrorMessage(__('No barcode has been imported.'));
                        $this->backendSession->setData('error_import', false);
                        $this->backendSession->setData('sku_exist', null);
                        $this->backendSession->setData('barcode_exist', null);
                    }
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__('Invalid file upload attempt'));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect->setUrl($this->_redirect->getRedirectUrl());
        return $resultRedirect;

    }
}
