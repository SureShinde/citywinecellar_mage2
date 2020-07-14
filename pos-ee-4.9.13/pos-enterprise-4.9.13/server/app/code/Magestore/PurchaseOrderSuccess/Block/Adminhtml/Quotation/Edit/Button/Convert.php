<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\Quotation\Edit\Button;

use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Status;

/**
 * Class Convert
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\Quotation\Edit\Button
 */
class Convert extends \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Button\AbstractButton
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::convert_quotation';
    
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        if(!$this->authorization->isAllowed(self::ADMIN_RESOURCE))
            return [];
        $purchaseOrder = $this->registry->registry('current_purchase_order');
        $purchaseOrderId = $purchaseOrder->getId();
        $type = $purchaseOrder->getType();
        $status = $purchaseOrder->getStatus();
        if($purchaseOrderId && $type == Type::TYPE_QUOTATION && $status == Status::STATUS_COMFIRMED){
            $url = $this->getUrl('purchaseordersuccess/quotation/convert', [
                'id' => $purchaseOrderId,
                'type' => $type
            ]);
            return [
                'label' => __('Convert Quotation to PO'),
                'class' => 'save primary',
                'on_click' => sprintf("function(e){
                        if(!confirm('Are you sure you want to convert this quotation to purchase order?')){
                            e.stopPropagation();
                        }
                    }"),
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'os_purchase_order_form.os_purchase_order_form',
                                    'actionName' => 'save',
                                    'params' => [
                                        true,
                                        ['convert' => 'true']
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
