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
 * Class GetPaymentAfter
 * @package Magestore\WebposZippay\Observer
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
        /** @var \Magestore\WebposZippay\Helper\Data $zippayHelper */
        $zippayHelper = $this->objectManager->create('Magestore\WebposZippay\Helper\Data');
        $payments = $observer->getData('payments');
        $paymentList = $payments->getList();
        $isZippayEnable = $zippayHelper->isEnableZippay();
        if ($isZippayEnable) {
            $zippayPayment = $this->addWebposZippay();
            $paymentList[] = $zippayPayment->getData();
        }
        $payments->setList($paymentList);
    }

    /**
     * @return \Magestore\Webpos\Model\Payment\Payment
     */
    public function addWebposZippay()
    {
        /** @var \Magestore\Webpos\Helper\Payment $paymentHelper */
        $paymentHelper = $this->objectManager->create('Magestore\Webpos\Helper\Payment');
        $sortOrder = $paymentHelper->getStoreConfig('webpos/payment/zippay/sort_order');
        $sortOrder = $sortOrder ? (int)$sortOrder : 0;
        /** @var \Magestore\WebposZippay\Helper\Data $zippayHelper */
        $zippayHelper = $this->objectManager->create('Magestore\WebposZippay\Helper\Data');
        $paymentModel = $this->objectManager->create('Magestore\Webpos\Model\Payment\Payment');

        $paymentModel->setCode(ZippayPaymentIntegration::CODE);
        $paymentModel->setTitle(__($zippayHelper->getPaymentTitle())->getText());
        $paymentModel->setApiUrl($zippayHelper->getApiUrl());
        $paymentModel->setLocations(json_encode($zippayHelper->getLocationMap()));
        $paymentModel->setInformation('');
        $paymentModel->setType(4);
        $paymentModel->setIsDefault(0);
        $paymentModel->setIsReferenceNumber(0);
        $paymentModel->setIsPayLater(0);
        $paymentModel->setMultiable(1);
        $paymentModel->setSortOrder($sortOrder);
        return $paymentModel;
    }
}