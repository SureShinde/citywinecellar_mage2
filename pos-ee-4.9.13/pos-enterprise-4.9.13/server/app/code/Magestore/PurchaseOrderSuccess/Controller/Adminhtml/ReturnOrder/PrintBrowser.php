<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder;

/**
 * Class Save
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder
 */
class PrintBrowser extends AbstractAction
{
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::print_return_order';

    public function execute()
    {
        $returnId = $this->getRequest()->getParam('return_id');
        $returnOrder = $this->returnOrderRepository->get($returnId);
        if ($returnOrder && $returnOrder->getReturnOrderId()) {
            $this->_registry->register('current_return_order', $returnOrder);
            try {
                $supplier = $this->supplierRepository->getById($returnOrder->getSupplierId());
                $this->_registry->register('current_return_order_supplier', $supplier);

                $warehouse = $this->sourceRepository->get($returnOrder->getWarehouseId());
                $this->_registry->register('current_return_order_warehouse', $warehouse);

                $html = $this->_resultPageFactory->create()->getLayout()->getBlock('print-header')->toHtml();
                $html .= $this->_resultPageFactory->create()->getLayout()->getBlock('print-items')->toHtml();
                return $this->getResponse()->setBody($html);
            } catch (\Exception $e) {
                return $this->redirectForm(
                    $returnId,
                    __('Could not print this Return Request'),
                    \Magento\Framework\Message\MessageInterface::TYPE_ERROR
                );
            }
        } else {
            return $this->redirectForm(
                $returnId,
                __('Can\'t find the Return Request to print.'),
                \Magento\Framework\Message\MessageInterface::TYPE_ERROR
            );
        }
    }
}