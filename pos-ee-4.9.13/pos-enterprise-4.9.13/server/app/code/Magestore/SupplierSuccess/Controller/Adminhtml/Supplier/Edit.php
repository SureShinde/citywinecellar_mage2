<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Controller\Adminhtml\Supplier;

use \Magestore\SupplierSuccess\Controller\Adminhtml\AbstractSupplier;

class Edit extends AbstractSupplier
{
    const ADMIN_RESOURCE = 'Magestore_SupplierSuccess::view_supplier';
    /**
     * @return void
     */
    public function execute()
    {
        $this->locator->unsetSession(\Magestore\SupplierSuccess\Api\Data\SupplierInterface::SUPPLIER_ID);
        $id = $this->getRequest()->getParam('id');
        $this->locator->setSession(\Magestore\SupplierSuccess\Api\Data\SupplierInterface::SUPPLIER_ID, $id);
        /** @var \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface $supplierRepository */
        $supplierRepository = $this->_objectManager->get(
            'Magestore\SupplierSuccess\Api\SupplierRepositoryInterface'
        );

        if ($id) {
            try {
                $model = $supplierRepository->getById($id);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This supplier no longer exists.'));
                $this->_redirect('*/*/*');
                return;
            }
        } else {
            /** @var \Magestore\SupplierSuccess\Model\Supplier $model */
            $model = $this->_objectManager->create('Magestore\SupplierSuccess\Model\Supplier');
        }

        $this->_coreRegistry->register(\Magestore\SupplierSuccess\Api\Data\SupplierInterface::CURRENT_SUPPLIER, $model);

        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Magestore_SupplierSuccess::supplier'
        )->_addBreadcrumb(
            __('Supplier Success'),
            __('Manage Suppliers')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Suppliers'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getSupplierName() : __('New Supplier')
        );

        $breadcrumb = $id ? __('Edit Supplier') : __('New Supplier');
        $this->_addBreadcrumb($breadcrumb, $breadcrumb);
        $this->_view->renderLayout();
    }
}
