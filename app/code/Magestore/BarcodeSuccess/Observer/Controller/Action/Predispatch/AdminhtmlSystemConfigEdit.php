<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Observer\Controller\Action\Predispatch;
use Magento\Framework\Event\Observer;

class AdminhtmlSystemConfigEdit implements \Magento\Framework\Event\ObserverInterface
{
    protected $helperData;
    protected $session;

    public function __construct(
        \Magestore\BarcodeSuccess\Helper\Data $helperData,
        \Magento\Backend\Model\Session $session
    ){
        $this->helperData = $helperData;
        $this->session = $session;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $controllerAction = $observer->getControllerAction();
        if ($controllerAction->getRequest()->getParam('section') == 'barcodesuccess') {
            $attributeCode = $this->helperData->getAttributeCode();
            $this->session->setAttributeCode($attributeCode);
        }
    }
}