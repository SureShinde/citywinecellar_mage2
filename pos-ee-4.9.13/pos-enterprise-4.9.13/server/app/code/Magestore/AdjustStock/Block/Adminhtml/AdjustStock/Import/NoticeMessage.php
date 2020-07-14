<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Block\Adminhtml\AdjustStock\Import;
/**
 * Class NoticeMessage
 * @package Magestore\AdjustStock\Block\Adminhtml\Import
 */
class NoticeMessage extends \Magento\Backend\Block\Template
{
    /**
     * @var
     */
    protected $backendSession;

    /**
     * NoticeMessage constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context
    ) {
        $this->backendSession = $context->getBackendSession();
        parent::__construct($context);
    }


    /**
     * @return mixed
     */
    public function getNumberSkuInvalid()
    {
        return $this->backendSession->getData('sku_invalid', true);
    }

    /**
     * @return mixed
     */
    public function isHasError()
    {
        return $this->backendSession->getData('error_import', true);
    }

    /**
     * @return mixed
     */
    public function getInvalidFileCsvUrl()
    {
        return $this->getUrl('adjuststock/adjuststock/downloadinvalidcsv');
    }
}