<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Observer\Admin\System\Config;
use Magento\Framework\Event\Observer;

class ChangeSectionBarcode implements \Magento\Framework\Event\ObserverInterface
{
    protected $helperData;
    protected $session;
    protected $attributeHelper;

    public function __construct(
        \Magestore\BarcodeSuccess\Helper\Data $helperData,
        \Magento\Backend\Model\Session $session,
        \Magestore\BarcodeSuccess\Helper\Attribute $attributeHelper
    ){
        $this->helperData = $helperData;
        $this->session = $session;
        $this->attributeHelper = $attributeHelper;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $attributeCode = $this->helperData->getAttributeCode();
        $oldCode = $this->session->getAttributeCode();
        if($attributeCode != $oldCode){
            $this->attributeHelper->importToBarcode($attributeCode);
        }

    }
}