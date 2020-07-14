<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\ReturnOrder\Edit\Button;

use Magestore\PurchaseOrderSuccess\Model\ReturnOrder\Option\Status;

/**
 * Class SendRequest
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Button
 */
class SendRequest extends \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Button\AbstractButton
{
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::send_return_order_request';

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
        if($returnOrderId && $status != Status::STATUS_CANCELED){
            $label = 'Send Email';
            if($status == Status::STATUS_PENDING)
                $label = 'Send Request';
            $url = $this->getUrl(
                'purchaseordersuccess/returnOrder/sendRequest',
                ['return_id' => $returnOrderId]
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
                                    'targetName' => 'os_return_order_form.os_return_order_form',
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
        }
        return [];
    }
}
