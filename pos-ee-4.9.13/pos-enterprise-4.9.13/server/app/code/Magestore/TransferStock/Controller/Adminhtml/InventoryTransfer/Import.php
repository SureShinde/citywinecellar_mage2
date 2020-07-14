<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer;
use Magento\Framework\Controller\ResultFactory;
/**
 * History Grid Holiday Action.
 *
 * @category Magestore
 * @package  Magestore_TransferStock
 * @module   TransferStock
 * @author   Magestore Developer
 */
class Import extends \Magestore\TransferStock\Controller\Adminhtml\InventoryTransfer\InventoryTransfer
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        if ($this->getRequest()->isPost()) {
            try {
                /** @var \Magestore\TransferStock\Model\InventoryTransfer\CsvImportHandler $importHandler */
                $importHandler = $this->_objectManager->create('Magestore\TransferStock\Model\InventoryTransfer\CsvImportHandler');
                $result = $importHandler->importFromCsvFile($this->getRequest()->getFiles('import_product'));
                if($result['status']) {
                    $this->messageManager->addSuccessMessage(__($result['message']));
                } else {
                    $this->messageManager->addErrorMessage(__($result['message']));
                }

            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Invalid file upload attempt'));
            }
        } else {
            $this->messageManager->addErrorMessage(__('Invalid file upload attempt'));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRedirectUrl());
        return $resultRedirect;

    }
}
