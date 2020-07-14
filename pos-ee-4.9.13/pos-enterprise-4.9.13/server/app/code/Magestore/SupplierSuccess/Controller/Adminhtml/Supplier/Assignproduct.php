<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Controller\Adminhtml\Supplier;

use Magento\Framework\Controller\ResultFactory;

class Assignproduct extends \Magestore\SupplierSuccess\Controller\Adminhtml\AbstractSupplier
{
    /**
     * Promo quote save action
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();

        $data = $this->getRequest()->getPostValue();
        if (isset($data['supplier_products'])) {
            $supplierProducts = json_decode($data['supplier_products'], true);
            $supplierId = $data['items'][0]['supplier_id'];
            $dataProductUpdate = [];
            foreach ($supplierProducts as $productId => $columns) {
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
