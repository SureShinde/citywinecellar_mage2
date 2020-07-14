<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Controller\Adminhtml\Supplier\Pricinglist;

use Magento\Framework\Controller\ResultFactory;
use Magestore\SupplierSuccess\Controller\Adminhtml\AbstractSupplier;

/**
 * Class Import
 * @package Magestore\SupplierSuccess\Controller\Adminhtml\Supplier\Pricinglist
 */
class Save extends AbstractSupplier
{
    const ADMIN_RESOURCE = 'Magestore_SupplierSuccess::supplier_pricinglist_edit';
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getParams();
        if (!isset($data['supplier_id'])) {
            $this->messageManager->addErrorMessage(__('Please select a supplier to create pricelist'));
            return $resultRedirect->setPath('suppliersuccess/supplier_pricinglist/index');
        }
        if (!isset($data['links']['dynamic_grid'])) {
            $this->messageManager->addErrorMessage(__('Please select product(s) to create pricelist'));
            return $resultRedirect->setPath('suppliersuccess/supplier_pricinglist/index');
        }
        try {
            /** @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Collection $supplierCollection */
            $supplierCollection = $this->supplierCollectionFactory->create();
            $supplierIds = $supplierCollection->getColumnValues('supplier_id');
            $supplierId = $data['supplier_id'];
            if (!$supplierId || !in_array($supplierId, $supplierIds)) {
                $this->messageManager->addErrorMessage(__('Please select supplier to create pricelist!'));
                $resultRedirect->setPath('suppliersuccess/supplier_pricinglist/index');
                return $resultRedirect;
            }
            $postData = $data['links']['dynamic_grid'];
            $pricingListData = [];
            foreach ($postData as $post) {
                $filterValues = [];
                $post['product_id'] = $post['id'];
                unset($post['id']);
                unset($post['position']);
                unset($post['record_id']);
                $post['supplier_id'] = $supplierId;
                if ($post['start_date']) {
                    if (array_key_exists('start_date', $post) && $post['start_date']) {
                        $filterValues['start_date'] = $this->dateFilter;
                    }
                } else {
                    $post['start_date'] = null;
                }
                if ($post['end_date']) {
                    if (array_key_exists('end_date', $post) && $post['end_date']) {
                        $filterValues['end_date'] = $this->dateFilter;
                    }
                } else {
                    $post['end_date'] = null;
                }

                $inputFilter = new \Zend_Filter_Input(
                    $filterValues,
                    [],
                    $post
                );
                $post = $inputFilter->getUnescaped();

                $pricingListData[] = $post;
            }
            /** create pricelist */
            $this->supplierPricingListService->addPricingList($pricingListData);
            $this->messageManager->addSuccessMessage(__('The pricelist are created'));

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Please recheck your data'));
        }
        $resultRedirect->setPath('suppliersuccess/supplier_pricinglist/index');
        return $resultRedirect;

    }
}
