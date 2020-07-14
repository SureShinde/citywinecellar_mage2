<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\ReturnOrder\Edit\Button;

use Magestore\PurchaseOrderSuccess\Model\ReturnOrder\Option\Status;

/**
 * Class Transfer
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Button
 */
class Transfer extends \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Button\AbstractButton
{
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::transferred_return_order';

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
        if($returnOrderId && in_array($status, [Status::STATUS_PROCESSING]) &&
            $returnOrder->getTotalQtyReturned() >
            $returnOrder->getTotalQtyTransferred()
        ){
            return [
                'label' => __('Delivery Items'),
                'class' => 'save primary',
                'component' => 'Magestore_PurchaseOrderSuccess/js/form/components/button',
                'on_click' => '',
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'os_return_order_form.os_return_order_form.'
                                        . 'transferred_product_modal',
                                    'actionName' => 'openModal'
                                ],
                                [
                                    'targetName' => 'os_return_order_form.os_return_order_form.'
                                        . 'transferred_product_modal.'
                                        . 'os_return_order_transferred_product_form',
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
