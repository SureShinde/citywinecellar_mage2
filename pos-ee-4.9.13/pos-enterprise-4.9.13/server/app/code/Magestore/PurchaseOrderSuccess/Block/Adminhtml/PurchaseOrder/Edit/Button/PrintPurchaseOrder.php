<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Button;

use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Status;

/**
 * Class
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Button
 */
class PrintPurchaseOrder extends \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Button\AbstractButton
{
    const ADMIN_RESOURCE_PO = 'Magestore_PurchaseOrderSuccess::print_purchase_order';
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
        if($purchaseOrderId){

            if(!class_exists('mPDF')){
                $url = $this->getUrl('purchaseordersuccess/purchaseOrder/printBrowser', [
                    'purchase_id' => $purchaseOrderId, 'type' => $type]);
                $onClick = sprintf("window.open('%s', 'PrintWindow', 'width=500,height=500,top=200,left=200').print()", $url);
            }else {
                $url = $this->getUrl('purchaseordersuccess/purchaseOrder/printPurchaseOrder', [
                    'purchase_id' => $purchaseOrderId, 'type' => $type]);
                $onClick = sprintf("setLocation('%s')", $url);
            }
            return [
                'label' => __('Print'),
                'class' => 'save',
                'on_click' => $onClick
            ];
        }
        return [];
    }
}
