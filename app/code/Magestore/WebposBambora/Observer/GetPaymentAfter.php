<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposBambora\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class GetPaymentAfter
 * @package Magestore\WebposBambora\Observer
 */
class GetPaymentAfter implements ObserverInterface
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        /** @var \Magestore\WebposBambora\Helper\Data $bamboraHelper */
        $bamboraHelper = $this->objectManager->create('Magestore\WebposBambora\Helper\Data');
        $payments = $observer->getData('payments');
        $paymentList = $payments->getList();
        $isBamboraEnable = $bamboraHelper->isEnableBambora();
        if ($isBamboraEnable) {
            $bamboraPayment = $this->addWebposBambora();
            $paymentList[] = $bamboraPayment->getData();
        }
        $payments->setList($paymentList);
    }

    /**
     * @return \Magestore\Webpos\Model\Payment\Payment
     */
    public function addWebposBambora()
    {
        /** @var \Magestore\Webpos\Helper\Payment $paymentHelper */
        $paymentHelper = $this->objectManager->create('Magestore\Webpos\Helper\Payment');
        $sortOrder = $paymentHelper->getStoreConfig('webpos/payment/bambora/sort_order');
        $sortOrder = $sortOrder ? (int)$sortOrder : 0;
        $isDefault = ('bambora_integration' == $paymentHelper->getDefaultPaymentMethod()) ?
            \Magestore\Webpos\Api\Data\Payment\PaymentInterface::YES :
            \Magestore\Webpos\Api\Data\Payment\PaymentInterface::NO;
        /** @var \Magestore\WebposBambora\Helper\Data $bamboraHelper */
        $bamboraHelper = $this->objectManager->create('Magestore\WebposBambora\Helper\Data');
        $paymentModel = $this->objectManager->create('Magestore\Webpos\Model\Payment\Payment');
        $paymentModel->setCode('bambora_integration');
        $paymentModel->setTitle(__($bamboraHelper->getPaymentTitle()));
        $paymentModel->setInformation('');
        $paymentModel->setType(3);
        $paymentModel->setIsDefault($isDefault);
        $paymentModel->setIsReferenceNumber(1);
        $paymentModel->setIsPayLater(0);
        $paymentModel->setMultiable(1);
        $paymentModel->setSortOrder($sortOrder);
        return $paymentModel;
    }
}