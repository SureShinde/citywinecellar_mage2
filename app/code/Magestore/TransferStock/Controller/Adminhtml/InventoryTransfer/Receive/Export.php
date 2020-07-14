<?php
/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer\Receive;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Export
 * @package Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer\Receive
 */
class Export extends \Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer\InventoryTransfer
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

    /**
     * Export constructor.
     * @param \Magestore\TransferStock\Controller\Adminhtml\Context $context
     * @param \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\ReceiveProduct\CollectionFactory $collectionFactory
     * @param \Magento\Framework\File\Csv $csvProcessor
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        \Magestore\TransferStock\Controller\Adminhtml\Context $context,
        \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\ReceiveProduct\CollectionFactory $collectionFactory,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem $filesystem
    ){
        parent::__construct($context);
        $this->_collectionFactory = $collectionFactory;
        $this->csvProcessor = $csvProcessor;
        $this->fileFactory = $fileFactory;
        $this->filesystem = $filesystem;
    }

    /**
     * execute
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
            file_get_contents($filename),
            DirectoryList::VAR_DIR
        );
    }

    /**
     * get base dir media
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
    public function generateData() {
        $receiveId = $this->_request->getParam("id");
        $data = [];
        if($receiveId){
            /** @var \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\ReceiveProduct\Collection $receiveProductCollection */
            $receiveProductCollection = $this->_collectionFactory->create();
            $receiveProductCollection->getProductType();
            $receiveProductCollection->addFieldToFilter("receive_id", $receiveId);
            foreach ($receiveProductCollection as $product) {
                $data[]= array(
                    $product->getData('product_sku'),
                    $product->getData('product_name'),
                    $product->getData('product_type_id'),
                    (float) $product->getData('qty'),
                );
            }
        }
        return $data;
    }

}


