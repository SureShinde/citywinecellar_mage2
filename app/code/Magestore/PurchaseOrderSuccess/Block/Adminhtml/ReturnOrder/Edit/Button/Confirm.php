<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\ReturnOrder\Edit\Button;

use Magestore\PurchaseOrderSuccess\Model\ReturnOrder\Option\Status;

/**
 * Class Confirm
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Button
 */
class Confirm extends \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Button\AbstractButton
{
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::confirm_return_order';

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        if(!$this->authorization->isAllowed(self::ADMIN_RESOURCE)) {
            return [];
        }

        $returnOrder = $this->registry->registry('current_return_order');
        $returnOrderId = $returnOrder->getId();
        $status = $returnOrder->getStatus();
        $label = __('Confirm Request');
        if($returnOrderId && ($status == Status::STATUS_PENDING)){
//            $url = $this->getUrl('purchaseordersuccess/returnOrder/confirm', [
//                'return_order_id' => $returnOrderId, 'type' => $type]);
            return [
                'label' => $label,
                'class' => 'save primary',
//                'on_click' =>  sprintf("deleteConfirm(
//                        'Are you sure you want to confirm this return order?',
//                        '%s'
//                    )", $url)
                'on_click' => '',
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'os_return_order_form.os_return_order_form',
                                    'actionName' => 'save',
                                    'params' => [
                                        true,
                                        ['isConfirm' => true]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
            ];
        }
        return [];
    }
}
