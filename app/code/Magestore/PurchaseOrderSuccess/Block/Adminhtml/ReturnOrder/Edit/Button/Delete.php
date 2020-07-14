<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\ReturnOrder\Edit\Button;

use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type;
use Magestore\PurchaseOrderSuccess\Model\ReturnOrder\Option\Status;

/**
 * Class Cancel
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Button
 */
class Delete extends \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Button\AbstractButton
{
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::delete_return_order';

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
        if($returnOrderId && $status == Status::STATUS_CANCELED){
            $url = $this->getUrl('purchaseordersuccess/returnOrder/delete', [
                'return_id' => $returnOrderId]);
            return [
                'label' => __('Delete'),
                'class' => 'cancel',
                'on_click' => sprintf("deleteConfirm(
                        'Are you sure you want to delete this return order?', 
                        '%s'
                    )", $url)
            ];
        }
        return [];
    }
}
