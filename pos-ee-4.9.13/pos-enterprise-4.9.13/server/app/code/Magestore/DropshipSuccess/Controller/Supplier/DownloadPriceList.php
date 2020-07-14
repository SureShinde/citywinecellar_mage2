<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Controller\Supplier;
/**
 * Class DownloadPriceList
 * @package Magestore\DropshipSuccess\Controller\Supplier
 */
class DownloadPriceList extends \Magestore\DropshipSuccess\Controller\AbstractSupplier
{

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $supplierId = $this->getRequest()->getParam('supplier_id');
        $fileName = $this->getRequest()->getParam('filename');
        $fileUpload = $this->getRequest()->getParam('file_upload');
        $link = $this->pricelistUploadService->getPriceListLinkBySupplierAndUpload($supplierId, $fileUpload);
        return $this->_fileFactory->create(
            $fileName.'.csv',
            array(
                'type' => 'filename',
                'value' => $link,
                'rm' => false  // can delete file after use
            ),
            \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
        );
    }
}
