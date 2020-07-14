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
class Complete extends AbstractAction
{
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::complete_purchase_order';

    public function execute() {
        $returnId = $this->getRequest()->getParam('return_id');
        if($returnId) {
            try{
                $this->returnOrderRepository->complete($returnId);
                $returnOrder = $this->returnOrderRepository->get($returnId);

                $warehouse = $this->sourceRepository->get($returnOrder->getWarehouseId());
                $this->_registry->register('current_return_order_warehouse', $warehouse);

                $supplier = $this->supplierRepository->getById($returnOrder->getSupplierId());
                $this->_registry->register('current_purchase_order', $returnOrder);
                $this->_registry->register('current_purchase_order_supplier', $supplier);
                return $this->redirectForm($returnId, __('Return request have been completed.'));
            } catch(\Exception $e) {
                return $this->redirectForm(
                    $returnId,
                    __('Could not complete return order.'),
                    \Magento\Framework\Message\MessageInterface::TYPE_ERROR
                );
            }
        } else {
            return $this->redirectForm($returnId, __('Cannot complete that return order!'));
        }
    }
}