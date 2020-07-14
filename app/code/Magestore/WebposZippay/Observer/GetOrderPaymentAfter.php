<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposZippay\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magestore\WebposZippay\Model\ZippayPaymentIntegration;

/**
 * Class GetOrderPaymentAfter
 * @package Magestore\WebposZippay\Observer
 */
class GetOrderPaymentAfter implements ObserverInterface
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var \Magestore\WebposZippay\Helper\Data
     */
    protected $zipHelper;

    public function __construct(
        \Magestore\WebposZippay\Helper\Data $zipHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->zipHelper = $zipHelper;
        $this->objectManager = $objectManager;
    }

    /**
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        /** @var \Magestore\Webpos\Api\Data\Checkout\Order\PaymentInterface $payment */
        $payment = $observer->getData('payment');


        if (
            $payment->getMethod() !== ZippayPaymentIntegration::ONLINE_CODE
        ) {
           return;
        }

        $payment->setIncrementId($payment->getReferenceNumber());
        $payment->setMethod(ZippayPaymentIntegration::CODE);
        $payment->setTitle(__($this->zipHelper->getPaymentTitle())->getText());


        $additionalInformation = $payment->getData('additional_information');
        if (
            is_array($additionalInformation)
            && !empty($additionalInformation['receipt_number'])
        ) {
            $payment->setReferenceNumber($additionalInformation['receipt_number']);
        }
    }

}