<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Controller\Supplier;

use Magento\Customer\Model\Session;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Redirect extends \Magestore\DropshipSuccess\Controller\AbstractSupplier
{

    /**
     * @return $this|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getParam('dropship', null);
        if (!$data) {
            $resultRedirect->setPath('dropship/supplier/index');
            return $resultRedirect;
        }
        $data = $this->dropshipRequestService->decodeUrlDropship($data);
        if ($data['supplier_id']) {
            $supplierId = $data['supplier_id'];
            $supplier = $this->supplierRepository->getById($supplierId);
            if ($supplier->getId()) {
                $this->supplierSession->setSupplierAsLoggedIn($supplier);
                if ($data['dropship_id']) {
                    return $resultRedirect->setPath('dropship/dropshipRequest/viewDropship', ['dropship_id' => $data['dropship_id']]);
                }
            }
        }
        $resultRedirect->setPath('dropship/supplier/login');
        return $resultRedirect;
    }
}
