<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposTyro\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class GetPaymentAfter
 * @package Magestore\WebposTyro\Observer
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
        /** @var \Magestore\WebposTyro\Helper\Data $tyroHelper */
        $tyroHelper = $this->objectManager->create('Magestore\WebposTyro\Helper\Data');
        $payments = $observer->getData('payments');
        $paymentList = $payments->getList();
        $isTyroEnable = $tyroHelper->isEnableTyro();
        if ($isTyroEnable) {
            $tyroPayment = $this->addWebposTyro();
            $paymentList[] = $tyroPayment->getData();
        }
        $payments->setList($paymentList);
    }

    /**
     * @return \Magestore\Webpos\Model\Payment\Payment
     */
    public function addWebposTyro()
    {
        /** @var \Magestore\Webpos\Helper\Payment $paymentHelper */
        $paymentHelper = $this->objectManager->create('Magestore\Webpos\Helper\Payment');
        $sortOrder = $paymentHelper->getStoreConfig('webpos/payment/tyro/sort_order');
        $sortOrder = $sortOrder ? (int)$sortOrder : 0;
        $isDefault = (\Magestore\WebposTyro\Helper\Data::PAYMENT_CODE === $paymentHelper->getDefaultPaymentMethod()) ?
            \Magestore\Webpos\Api\Data\Payment\PaymentInterface::YES :
            \Magestore\Webpos\Api\Data\Payment\PaymentInterface::NO;
        /** @var \Magestore\WebposTyro\Helper\Data $tyroHelper */
        $tyroHelper = $this->objectManager->create('Magestore\WebposTyro\Helper\Data');
        $paymentModel = $this->objectManager->create('Magestore\Webpos\Model\Payment\Payment');

        $paymentModel->setCode(\Magestore\WebposTyro\Helper\Data::PAYMENT_CODE);
        $paymentModel->setTitle($tyroHelper->getPaymentTitle());
        $paymentModel->setMerchantId($tyroHelper->getMerchantId());
        $paymentModel->setApiKey($tyroHelper->getApiKey());
        $paymentModel->setMode($tyroHelper->getMode());
        $paymentModel->setInformation('');
        $paymentModel->setType(3);
        $paymentModel->setIsDefault($isDefault);
        $paymentModel->setIsReferenceNumber(0);
        $paymentModel->setIsPayLater(0);
        $paymentModel->setMultiable(1);
        $paymentModel->setSortOrder($sortOrder);
        return $paymentModel;
    }
}