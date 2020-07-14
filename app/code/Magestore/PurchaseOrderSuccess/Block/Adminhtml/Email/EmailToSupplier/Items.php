<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\Email\EmailToSupplier;

use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Status;

/**
 * Class Items
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\Email\EmailToSupplier
 */
class Items extends AbstractEmailToSupplier
{
    protected $_template = 'Magestore_PurchaseOrderSuccess::email/email_to_supplier/items.phtml';

    public function checkIsShowQtyReceived(){
        $status = $this->getPurchaseOrderData('status');
        return in_array($status, [Status::STATUS_COMPLETED, Status::STATUS_CANCELED]);
    }
}