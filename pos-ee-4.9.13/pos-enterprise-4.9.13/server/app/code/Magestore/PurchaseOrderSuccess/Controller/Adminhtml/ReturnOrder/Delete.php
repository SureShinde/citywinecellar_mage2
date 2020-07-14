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
class Delete extends AbstractAction
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::delete_return_order';

    public function execute() {
        $returnId = $this->getRequest()->getParam('return_id');
        if($returnId) {
            try{
                $this->returnOrderRepository->deleteById($returnId);
                return $this->redirectGrid(__('Return request have been deleted.'));
            } catch(\Exception $e) {
                return $this->redirectForm(
                    $returnId,
                    __('Could not delete return order.'),
                    \Magento\Framework\Message\MessageInterface::TYPE_ERROR
                );
            }
        } else {
            return $this->redirectForm($returnId, __('Cannot delete that return order!'));
        }
    }
}