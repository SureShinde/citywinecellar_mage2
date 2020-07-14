<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\SupplierSuccess\Controller\Adminhtml\Supplier\Pricinglist;

use \Magestore\SupplierSuccess\Controller\Adminhtml\AbstractSupplier;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class MassDelete
 */
class MassDelete extends AbstractSupplier
{
    const ADMIN_RESOURCE = 'Magestore_SupplierSuccess::supplier_pricinglist_edit';

    /**
     * @return $this
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->supplierPricingListCollectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $priceList) {
            $priceList->delete();
        }

        $this->messageManager->addSuccessMessage(__('%1 record(s) have been deleted.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
