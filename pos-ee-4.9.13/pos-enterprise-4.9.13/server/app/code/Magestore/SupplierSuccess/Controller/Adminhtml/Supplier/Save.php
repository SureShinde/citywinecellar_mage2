<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Controller\Adminhtml\Supplier;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magestore\SupplierSuccess\Controller\Adminhtml\AbstractSupplier
{
    const ADMIN_RESOURCE = 'Magestore_SupplierSuccess::view_supplier';

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('id');

            if (empty($data['supplier_id'])) {
                $data['supplier_id'] = null;
            }

            /** @var \Magestore\SupplierSuccess\Model\Supplier $model */
            $model = $this->_objectManager->create('Magestore\SupplierSuccess\Model\Supplier')->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This supplier no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            /** add new password */
            $newPassword = null;
            if ((isset($data['new_password']) && $data['new_password'])
                    || (isset($data['generated_password']) && $data['generated_password'])) {
                $newPassword = $this->supplierService->setPasswordSupplier($data['new_password'], $data['generated_password']);
                $data['password'] = md5($newPassword);
            }
            $model->setData($data);

            try {
                $model->save();
                /** send new password to supplier */
                if ($newPassword && isset($data['send_pass_to_supplier']) && $data['send_pass_to_supplier']) {
                    $this->supplierService->sendNewPasswordTosupplier($model, $newPassword);
                }
                $this->messageManager->addSuccessMessage(__('The supplier information has been saved.'));

                /** save product to supplier */
                if (isset($data['supplier_products'])) {
                    $supplierProducts = json_decode($data['supplier_products'], true);
                    $supplierId = $model->getId();
                    $dataProductUpdate = [];
                    $supplierProductsExist = $this->_supplierProductService->getProductsBySupplierId($supplierId)
                        ->getAllIds();
                    foreach ($supplierProducts as $productId => $columns) {
                        if (!$productId || !is_numeric($productId) || !in_array($productId, $supplierProductsExist)) {
                            continue;
                        }
                        $dataUpdate['supplier_id'] = $supplierId;
                        $dataUpdate['supplier_product_id'] = $productId;
                        $columnsUpdate = $columns;//json_decode($columns, true);
                        $dataUpdate = array_replace_recursive(
                            $dataUpdate,
                            $columnsUpdate
                        );
                        $dataProductUpdate[] = $dataUpdate;
                    }
                    if (!empty($dataProductUpdate)) {
                        try {
                            $this->_supplierProductService->assignProductToSupplier($dataProductUpdate);
                            $this->messageManager->addSuccessMessage(__('Products in this supplier have been updated.'));
                        } catch (\Exception $e) {
                            $this->messageManager->addErrorMessage(__($e->getMessage()));
                        }
                    }
                }

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the supplier information.'));
            }

            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
