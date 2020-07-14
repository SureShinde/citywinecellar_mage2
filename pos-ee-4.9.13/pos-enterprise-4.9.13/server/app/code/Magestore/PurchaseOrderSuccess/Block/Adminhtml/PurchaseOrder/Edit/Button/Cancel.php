<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Button;

use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Status;

/**
 * Class Cancel
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Button
 */
class Cancel extends \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Button\AbstractButton
{
    const ADMIN_RESOURCE_PO = 'Magestore_PurchaseOrderSuccess::cancel_purchase_order';
    const ADMIN_RESOURCE_QUOTATION = 'Magestore_PurchaseOrderSuccess::cancel_quotation';

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $purchaseOrder = $this->registry->registry('current_purchase_order');
        $purchaseOrderId = $purchaseOrder->getId();
        $status = $purchaseOrder->getStatus();
        $type = $purchaseOrder->getType();
        $typeLabel = $this->getTypeLabel($type);

        if(!$this->authorization->isAllowed(self::ADMIN_RESOURCE_QUOTATION)
            && $type == TYPE::TYPE_QUOTATION) {
            return [];
        }

        if(!$this->authorization->isAllowed(self::ADMIN_RESOURCE_PO)
            && $type == TYPE::TYPE_PURCHASE_ORDER) {
            return [];
        }

        if($purchaseOrderId && !in_array($status,[Status::STATUS_COMPLETED, Status::STATUS_CANCELED])){
            $url = $this->getUrl('purchaseordersuccess/purchaseOrder/cancel', [
                'purchase_id' => $purchaseOrderId, 'type' => $type]);
            return [
                'label' => __('Cancel'),
                'class' => 'cancel',
                'on_click' => sprintf("deleteConfirm(
                        'Are you sure you want to cancel this %s?', 
                        '%s'
                    )", $typeLabel, $url)
            ];
        }
        return [];
    }
}
