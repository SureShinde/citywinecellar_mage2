<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Button;

use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Status;

/**
 * Class Receive
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Button
 */
class Receive extends \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Button\AbstractButton
{
    const ADMIN_RESOURCE_PO = 'Magestore_PurchaseOrderSuccess::received_purchase_order';
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
        if($purchaseOrderId && $type == Type::TYPE_PURCHASE_ORDER && $status == Status::STATUS_PROCESSING
            && $purchaseOrder->getTotalQtyOrderred() > $purchaseOrder->getTotalQtyReceived()){
            return [
                'label' => __('Receive Items'),
                'class' => 'save primary',
                'component' => 'Magestore_PurchaseOrderSuccess/js/form/components/button',
                'on_click' => '',
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'os_purchase_order_form.os_purchase_order_form.'
                                        . 'received_product_modal',
                                    'actionName' => 'openModal'
                                ],
                                [
                                    'targetName' => 'os_purchase_order_form.os_purchase_order_form.'
                                        . 'received_product_modal.'
                                        . 'os_purchase_order_received_product_form',
                                    'actionName' => 'render'
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
