<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\SupplierSuccess\Controller\Adminhtml\Supplier;

use \Magestore\SupplierSuccess\Controller\Adminhtml\AbstractSupplier;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class MassDelete
 */
class MassDisable extends AbstractSupplier
{
    const ADMIN_RESOURCE = 'Magestore_SupplierSuccess::view_supplier';
    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->supplierCollectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $supplier) {
            $supplier->setStatus(\Magestore\SupplierSuccess\Service\SupplierService::STATUS_DISABLE);
            $supplier->save();
        }

        $this->messageManager->addSuccessMessage(__('%1 record(s) have been disabled.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
