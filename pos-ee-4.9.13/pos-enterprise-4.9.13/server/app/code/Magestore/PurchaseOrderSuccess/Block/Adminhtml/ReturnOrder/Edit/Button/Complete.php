<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\ReturnOrder\Edit\Button;

use Magestore\PurchaseOrderSuccess\Model\ReturnOrder\Option\Status;

/**
 * Class Complete
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Button
 */
class Complete extends \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Button\AbstractButton
{
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::complete_return_order';

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
        if($returnOrderId && $status == Status::STATUS_PROCESSING){
            $url = $this->getUrl('purchaseordersuccess/returnOrder/complete', [
                'return_id' => $returnOrderId]);
            return [
                'label' => __('Complete Request'),
                'class' => 'save primary',
                'on_click' =>  sprintf("deleteConfirm(
                        'Are you sure you want to complete this return order?', 
                        '%s'
                    )", $url)
            ];
        }
        return [];
    }
}
