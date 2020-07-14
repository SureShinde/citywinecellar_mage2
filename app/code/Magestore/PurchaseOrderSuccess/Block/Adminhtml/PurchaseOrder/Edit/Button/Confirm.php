<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Button;

use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Status;

/**
 * Class Confirm
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Button
 */
class Confirm extends \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Button\AbstractButton
{
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::revert_quotation';
    const ADMIN_RESOURCE_CONFIRM_PO = 'Magestore_PurchaseOrderSuccess::confirm_purchase_order';
    const ADMIN_RESOURCE_CONFIRM_QUOTATION = 'Magestore_PurchaseOrderSuccess::confirm_quotation';

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $purchaseOrder = $this->registry->registry('current_purchase_order');
        $purchaseOrderId = $purchaseOrder->getId();
        $type = $purchaseOrder->getType();
        $status = $purchaseOrder->getStatus();
        if($purchaseOrderId && ($status == Status::STATUS_PENDING) || ($status == Status::STATUS_COMFIRMED && $type == Type::TYPE_QUOTATION)){
            if($type == Type::TYPE_PURCHASE_ORDER) {
                return $this->getConfirmPo();
            } elseif($type == Type::TYPE_QUOTATION) {
                if($status == Status::STATUS_COMFIRMED) {
                    return $this->getRevertQo();
                } else {
                    return $this->getConfirmQo();
                }
            }
        }
        return [];
    }

    public function getConfirmPo() {
        if(!$this->authorization->isAllowed(self::ADMIN_RESOURCE_CONFIRM_PO)) {
            return [];
        }
        return [
            'label' => 'Confirm Purchase Order',
            'class' => 'save primary',
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
                                    ['isConfirm' => true]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }

    public function getConfirmQo() {
        if(!$this->authorization->isAllowed(self::ADMIN_RESOURCE_CONFIRM_QUOTATION)) {
            return [];
        }
        return [
            'label' => 'Confirm Quotation',
            'class' => 'save primary',
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
                                    ['isConfirm' => true]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }

    public function getRevertQo() {
        if(!$this->authorization->isAllowed(self::ADMIN_RESOURCE)) {
            return [];
        }
        return [
            'label' => 'Revert Quotation',
            'class' => 'save primary',
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
                                    ['isRevert' => true]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }
}
