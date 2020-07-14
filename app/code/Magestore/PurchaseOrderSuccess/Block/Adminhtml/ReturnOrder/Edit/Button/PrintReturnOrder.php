<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\ReturnOrder\Edit\Button;

use Magestore\PurchaseOrderSuccess\Model\ReturnOrder\Option\Status;

/**
 * Class
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Button
 */
class PrintReturnOrder extends \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Button\AbstractButton
{
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::print_return_order';

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
        if($returnOrderId){

            $url = $this->getUrl('purchaseordersuccess/returnOrder/printBrowser', [
                'return_id' => $returnOrderId]);
            $onClick = sprintf("window.open('%s', 'PrintWindow', 'width=500,height=500,top=200,left=200').print()", $url);

            return [
                'label' => __('Print'),
                'class' => 'save',
                'on_click' => $onClick
            ];
        }
        return [];
    }
}
