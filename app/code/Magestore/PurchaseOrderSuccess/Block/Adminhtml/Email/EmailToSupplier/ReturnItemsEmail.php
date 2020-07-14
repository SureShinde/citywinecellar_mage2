<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\Email\EmailToSupplier;

use Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemInterface;
use Magestore\PurchaseOrderSuccess\Model\ReturnOrder\Option\Status;

/**
 * Class Items
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\Email\EmailToSupplier
 */
class ReturnItemsEmail extends AbstractEmailToSupplier
{
    protected $_template = 'Magestore_PurchaseOrderSuccess::email/email_to_supplier/returnitems.phtml';
}