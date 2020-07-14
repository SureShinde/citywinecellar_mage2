<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Controller\Adminhtml\Supplier\Product;

/**
 * Class Deleterow
 * @package Magestore\SupplierSuccess\Controller\Adminhtml\Supplier\Product
 */
class Deleterow extends \Magestore\SupplierSuccess\Controller\Adminhtml\AbstractSupplier
{
    const ADMIN_RESOURCE = 'Magestore_SupplierSuccess::view_supplier';

    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        try{
            $this->supplierProductRepositoryInterface->deleteById($this->getRequest()->getParam('id'));
            $this->messageManager->addSuccessMessage(__('Deleted a product in this supplier.'));
        }catch (\Exception $e){
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }
        $layout = $this->layoutFactory->create();
        $layout->initMessages();
        $response['error'] = true;
        $response['messages'] = [$layout->getMessagesBlock()->getGroupedHtml()];
        return $resultJson->setData([
            'messages' => $response['messages'],
            'error' => $response['error']
        ]);

    }
}