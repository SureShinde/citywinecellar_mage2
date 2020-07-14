<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Button;

use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Status;

/**
 * Class SendRequest
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Button
 */
class SendRequest extends \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Button\AbstractButton
{
    const ADMIN_RESOURCE_PO = 'Magestore_PurchaseOrderSuccess::send_purchase_order_request';
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        if(!$this->authorization->isAllowed(self::ADMIN_RESOURCE_PO)) {
            return [];
        }

        $purchaseOrder = $this->registry->registry('current_purchase_order');
        $purchaseOrderId = $purchaseOrder->getId();
        $type = $purchaseOrder->getType();
        $status = $purchaseOrder->getStatus();
        if($purchaseOrderId && $status != Status::STATUS_CANCELED){
            $label = 'Send Email';
            if($status == Status::STATUS_PENDING)
                $label = 'Send Request';
            $url = $this->getUrl(
                'purchaseordersuccess/purchaseOrder/sendRequest', 
                ['purchase_id' => $purchaseOrderId, 'type' => $type]
            );
            return [
                'label' => $label,
                'class' => 'save',
                'on_click' => '',
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'os_purchase_order_form.os_purchase_order_form',
                                    'actionName' => 'save',
                                    'params' => [
                                        true,
                                        ['sendEmail' => 'true'],
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
            ];
            return [
                'label' => __($label),
                'class' => 'save primary',
                'on_click' => sprintf("location.href = '%s'", $url)
            ];
        }
        return [];
    }
}
