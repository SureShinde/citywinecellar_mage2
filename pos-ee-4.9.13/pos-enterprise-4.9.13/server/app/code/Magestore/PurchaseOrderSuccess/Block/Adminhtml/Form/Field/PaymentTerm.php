<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\Form\Field;

use Magestore\BarcodeSuccess\Block\Adminhtml\Template\Description;

class PaymentTerm extends ShippingMethod
{
    /**
     * @var bool
     */
    protected $_hasDescription = true;
}