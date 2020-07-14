<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Button;

use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Status;

/**
 * Class Complete
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Button
 */
class Complete extends \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Button\AbstractButton
{
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::complete_purchase_order';

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        if (!$this->authorization->isAllowed(self::ADMIN_RESOURCE)) {
            return [];
        }

        $purchaseOrder = $this->registry->registry('current_purchase_order');
        $purchaseOrderId = $purchaseOrder->getId();
        $type = $purchaseOrder->getType();
        $status = $purchaseOrder->getStatus();
        if ($purchaseOrderId && $type == Type::TYPE_PURCHASE_ORDER && $status == Status::STATUS_PROCESSING) {
            $url = $this->getUrl('purchaseordersuccess/purchaseOrder/complete', [
                'purchase_id' => $purchaseOrderId, 'type' => $type]);
            $popMessage = sprintf("deleteConfirm(
                        'Are you sure you want to complete this purchase order?',
                        '%s'
                    )", $url);
            if ($purchaseOrder->getTotalQtyOrderred() > 0
                && $purchaseOrder->getTotalQtyReceived() < $purchaseOrder->getTotalQtyOrderred()) {
                $popMessage = sprintf("deleteConfirm(
                        'You haven\'t received enough Qty Ordered for this Purchase Order. Are you sure you want to complete it?',
                        '%s'
                    )", $url);
            }
            return [
                'label' => __('Complete PO'),
                'class' => 'save primary',
                'on_click' => $popMessage
            ];
        }
        return [];
    }
}
