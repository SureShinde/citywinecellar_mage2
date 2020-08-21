<?php
/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer\Receive;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\ReceiveProduct\CollectionFactory
    as ReceiveProductCollectionFactory;
use \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\ReceiveProduct\Collection
    as ReceiveProductCollection;

/**
 * Export receive
 */
class Export extends \Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer\InventoryTransfer implements
    HttpGetActionInterface
{
    /**
     * @var \Magento\Framework\File\Csv
     */
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
     * @var \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\ReceiveProduct\CollectionFactory
     */
    protected $_collectionFactory;

    protected $filesystemDriver;

    /**
     * Export constructor.
     *
     * @param \Magestore\TransferStock\Controller\Adminhtml\Context $context
     * @param ReceiveProductCollectionFactory $collectionFactory
     * @param \Magento\Framework\File\Csv $csvProcessor
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\DriverInterface $filesystemDriver
     */
    public function __construct(
        \Magestore\TransferStock\Controller\Adminhtml\Context $context,
        ReceiveProductCollectionFactory $collectionFactory,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\DriverInterface $filesystemDriver
    ) {
        parent::__construct($context);
        $this->_collectionFactory = $collectionFactory;
        $this->csvProcessor = $csvProcessor;
        $this->fileFactory = $fileFactory;
        $this->filesystem = $filesystem;
        $this->filesystemDriver = $filesystemDriver;
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute()
    {
        $name = 'received_list.csv';
        $this->getBaseDirMedia()->create('magestore/transferstock');
        $filename = $this->getBaseDirMedia()->getAbsolutePath('magestore/transferstock/'.$name);
        $data = [
            ['SKU', 'Name', 'Type', 'Qty received']
        ];
        $data = array_merge($data, $this->generateData());
        $this->csvProcessor->saveData($filename, $data);
        return $this->fileFactory->create(
            $name,
            $this->filesystemDriver->fileGetContents($filename),
            DirectoryList::VAR_DIR
        );
    }

    /**
     * Get base dir media
     *
     * @return \Magento\Framework\Filesystem\Directory\WriteInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getBaseDirMedia()
    {
        return $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    /**
     * Generate data
     *
     * @return array
     */
    public function generateData()
    {
        $receiveId = $this->_request->getParam("id");
        $data = [];
        if ($receiveId) {
            /** @var ReceiveProductCollection $receiveProductCollection */
            $receiveProductCollection = $this->_collectionFactory->create();
            $receiveProductCollection->getProductType();
            $receiveProductCollection->addFieldToFilter("receive_id", $receiveId);
            foreach ($receiveProductCollection as $product) {
                $data[]= [
                    $product->getData('product_sku'),
                    $product->getData('product_name'),
                    $product->getData('product_type_id'),
                    (float) $product->getData('qty'),
                ];
            }
        }
        return $data;
    }
}
