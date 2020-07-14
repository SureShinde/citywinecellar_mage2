<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Block\Adminhtml\Barcode\Import;
class NoticeMessage extends \Magento\Backend\Block\Template
{
    protected $backendSession;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context
    ) {
        $this->backendSession = $context->getBackendSession();
        parent::__construct($context);
    }

    public function isHasError()
    {
        return $this->backendSession->getData('error_import', true);

    }

    public function getNumberBarCodeExist()
    {
        return $this->backendSession->getData('barcode_exist', true);
    }

    public function getNumberSkuExist()
    {
        return $this->backendSession->getData('sku_exist', true);
    }

}