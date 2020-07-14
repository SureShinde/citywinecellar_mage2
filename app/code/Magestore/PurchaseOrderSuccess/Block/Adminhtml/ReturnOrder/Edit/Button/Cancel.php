<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\ReturnOrder\Edit\Button;

use Magestore\PurchaseOrderSuccess\Model\ReturnOrder\Option\Status;

/**
 * Class Cancel
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Button
 */
class Cancel extends \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Button\AbstractButton
{
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::cancel_return_order';

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
        if($returnOrderId && !in_array($status,[Status::STATUS_COMPLETED, Status::STATUS_CANCELED])){
            $url = $this->getUrl('purchaseordersuccess/returnOrder/cancel', [
                'return_id' => $returnOrderId]);
            return [
                'label' => __('Cancel'),
                'class' => 'cancel',
                'on_click' => sprintf("deleteConfirm(
                        'Are you sure you want to cancel this return order?', 
                        '%s'
                    )", $url)
            ];
        }
        return [];
    }
}
