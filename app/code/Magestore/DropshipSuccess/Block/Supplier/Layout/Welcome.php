<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Block\Supplier\Layout;

use Magestore\DropshipSuccess\Block\Supplier\AbstractSupplier;

/**
 * Class Welcome
 * @package Magestore\DropshipSuccess\Block\Supplier\Layout
 */
class Welcome extends AbstractSupplier
{

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getWelcomeMessage()
    {
        $supplier = $this->supplierSession->getSupplier();
        if ($supplier->getId()) {
            return __('Welcome %1(%2)', $supplier->getSupplierName(), $supplier->getContactName());
        }
        return __('Welcome to Dropship Management!');
    }
}