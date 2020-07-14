<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Button;

use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Status;

/**
 * Class Save
 */
class Save extends \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Button\AbstractButton
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $purchaseOrder = $this->registry->registry('current_purchase_order');
        $purchaseOrderId = $purchaseOrder->getId();
        $status = $purchaseOrder->getStatus();
        if(in_array($status,[Status::STATUS_COMPLETED, Status::STATUS_CANCELED])){
            return [];
        }
        $buttonLabel = __('Prepare Product List');
        if($purchaseOrder && $purchaseOrderId){
            $buttonLabel = __('Save');
        }
        $primary = $status!=Status::STATUS_PROCESSING?'primary':'';
        return [
            'label' => $buttonLabel,
            'class' => 'save '.$primary,
            'on_click' => '',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'os_purchase_order_form.os_purchase_order_form',
                                'actionName' => 'save',
                                'params' => [
                                    true
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }
}
