<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher;
use Magento\Framework\Component\ComponentRegistrar;

/**
 * Download Sample gift voucher
 */
class DownloadSample extends Giftvoucher implements HttpGetActionInterface
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var ComponentRegistrar
     */
    protected $componentRegistrar;

    /**
     * @var \Magento\Framework\Filesystem\DriverInterface
     */
    protected $fileSystemDriver;

    /**
     * DownloadSample constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magestore\Giftvoucher\Model\GiftvoucherFactory $modelFactory
     * @param \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param ComponentRegistrar $componentRegistrar
     * @param \Magento\Framework\Filesystem\DriverInterface $fileSystemDriver
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magestore\Giftvoucher\Model\GiftvoucherFactory $modelFactory,
        \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\CollectionFactory $collectionFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        ComponentRegistrar $componentRegistrar,
        \Magento\Framework\Filesystem\DriverInterface $fileSystemDriver
    ) {
        parent::__construct(
            $context,
            $resultPageFactory,
            $resultLayoutFactory,
            $resultForwardFactory,
            $coreRegistry,
            $modelFactory,
            $collectionFactory
        );
        $this->fileFactory = $fileFactory;
        $this->componentRegistrar = $componentRegistrar;
        $this->fileSystemDriver = $fileSystemDriver;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $filename = $this->componentRegistrar->getPath(
            ComponentRegistrar::MODULE,
            'Magestore_Giftvoucher'
        );
        $filename .= '/fixtures/import_giftcode_sample.csv';

        return $this->fileFactory->create(
            'import_giftcode_sample.csv',
            $this->fileSystemDriver->fileGetContents($filename),
            DirectoryList::VAR_DIR
        );
    }
}
