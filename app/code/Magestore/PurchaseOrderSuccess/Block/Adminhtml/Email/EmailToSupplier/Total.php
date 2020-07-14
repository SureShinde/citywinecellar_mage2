<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\Email\EmailToSupplier;

use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface;

/**
 * Class EmailToSupplier
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\Email
 */
class Total extends AbstractEmailToSupplier
{
    protected $_template = 'Magestore_PurchaseOrderSuccess::email/email_to_supplier/total.phtml';

    protected $width = '100%';

    public function getWidth(){
        return $this->width;
    }

    public function setWidth($width){
        $this->width = $width;
        return $this;
    }
}