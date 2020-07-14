<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposAuthorizenet\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class GetPaymentAfter
 * @package Magestore\WebposAuthorizenet\Observer
 */
class GetPaymentAfter implements ObserverInterface
{
    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $authorizenetHelper = \Magento\Framework\App\ObjectManager::getInstance()
                         ->create('Magestore\WebposAuthorizenet\Helper\Data');
        $payments = $observer->getData('payments');
        $paymentList = $payments->getList();
        $isAuthorizenetEnable = $authorizenetHelper->isEnableAuthorizenet();
        if($isAuthorizenetEnable) {
            $authorizenetPayment = $this->addWebposAuthorizenet();
            $paymentList[] = $authorizenetPayment->getData();
        }
        $payments->setList($paymentList);
    }

    /**
     * @return \Magestore\Webpos\Model\Payment\Payment
     */
    public function addWebposAuthorizenet()
    {
        $paymentHelper = \Magento\Framework\App\ObjectManager::getInstance()
                            ->create('Magestore\Webpos\Helper\Payment');
        $helper = \Magento\Framework\App\ObjectManager::getInstance()
                        ->create('Magestore\Webpos\Helper\Data');
        $isSandbox = $helper->getStoreConfig('webpos/payment/authorizenet/is_sandbox');
        $apiLogin = $helper->getStoreConfig('webpos/payment/authorizenet/api_login');
        $clientId = $helper->getStoreConfig('webpos/payment/authorizenet/client_id');
        $title = $helper->getStoreConfig('webpos/payment/authorizenet/title');
        $sortOrder = $helper->getStoreConfig('webpos/payment/authorizenet/sort_order');
        $sortOrder = $sortOrder ? (int)$sortOrder : 0;
        $isDefault = ('authorizenet_integration' == $paymentHelper->getDefaultPaymentMethod()) ?
            \Magestore\Webpos\Api\Data\Payment\PaymentInterface::YES :
            \Magestore\Webpos\Api\Data\Payment\PaymentInterface::NO;
        $paymentModel = \Magento\Framework\App\ObjectManager::getInstance()
                            ->create('Magestore\Webpos\Model\Payment\Payment');
        $paymentModel->setCode('authorizenet_integration');
        $paymentModel->setTitle($title);
        $paymentModel->setInformation('');
        $paymentModel->setType('1');
        $paymentModel->setIsDefault($isDefault);
        $paymentModel->setIsReferenceNumber(0);
        $paymentModel->setIsPayLater(0);
        $paymentModel->setMultiable(1);
        $paymentModel->setApiLogin($apiLogin);
        $paymentModel->setClientId($clientId);
        $paymentModel->setIsSandbox((int)$isSandbox);
        $paymentModel->setSortOrder($sortOrder);
        return $paymentModel;
    }
}