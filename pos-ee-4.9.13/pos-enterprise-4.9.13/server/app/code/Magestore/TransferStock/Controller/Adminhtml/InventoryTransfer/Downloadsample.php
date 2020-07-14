<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class DownloadSample
 * @package Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer
 */
class DownloadSample extends \Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer\InventoryTransfer
{
    const SAMPLE_QTY = 1;

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute()
    {
        $name = md5(microtime());
        $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->create('import');
        $filename = DirectoryList::VAR_DIR.'/import/'.$name.'.csv';

        $stream = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->openFile($filename, 'w+');
        $stream->lock();
        $qtyLabel = __('Qty to Send');
        $skuLabel = __('SKU');
        $data = array(
            array($skuLabel,$qtyLabel)
        );
        $data = array_merge($data, $this->generateSampleData(3));
        foreach ($data as $row) {
            $stream->writeCsv($row);
        }
        $stream->unlock();
        $stream->close();

        return $this->fileFactory->create(
            'import_product_to_inventorytransfer.csv',
            array(
                'type' => 'filename',
                'value' => $filename,
                'rm' => true  // can delete file after use
            ),
            DirectoryList::VAR_DIR
        );
    }

    /**
     * generate sample data
     *
     * @param int
     * @return array
     */
    public function generateSampleData($number)
    {
        $data = [];
        $productCollection = $this->_objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection')
            ->setPageSize($number)
            ->setCurPage(1);
        /** @var \Magestore\TransferStock\Api\Data\InventoryTransfer\InventoryTransferInterface $transferStock */
        $transferStock = $this->inventoryTransferFactory->create()->load($this->getRequest()->getParam('id'));
        $productCollection->getSelect()->joinInner(
            ['source_item' => $this->inventoryTransferResource->getTable('inventory_source_item')],
            'e.sku = source_item.sku AND source_item.source_code = "' . $transferStock->getSourceWarehouseCode() . '"',
            []
        );
        foreach ($productCollection as $productModel) {
            $data[]= array($productModel->getData('sku'), self::SAMPLE_QTY);
        }
        return $data;
    }
}
