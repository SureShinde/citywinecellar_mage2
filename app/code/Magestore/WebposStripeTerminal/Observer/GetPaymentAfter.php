<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposStripeTerminal\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magestore\WebposStripeTerminal\Api\StripeTerminalServiceInterface;

/**
 * Class GetPaymentAfter
 * @package Magestore\WebposStripeTerminal\Observer
 */
class GetPaymentAfter implements ObserverInterface
{
    /**
     * @var \Magestore\WebposStripeTerminal\Helper\Data
     */
    protected $helper;

    public function __construct()
    {
        $this->helper = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magestore\WebposStripeTerminal\Helper\Data');
    }


    /**
     * @param EventObserver $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(EventObserver $observer)
    {

        $payments = $observer->getData('payments');
        $paymentList = $payments->getList();
        $isEnabled = $this->helper->isEnabled();
        if ($isEnabled) {
            $payment = $this->add();
            $paymentList[] = $payment->getData();
        }
        $payments->setList($paymentList);
    }

    /**
     * @return \Magestore\Webpos\Model\Payment\Payment
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function add()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /**
         * @var \Magestore\WebposStripeTerminal\Helper\Data $stripeterminalHelper
         */
        $paymentModel = $objectManager->create('Magestore\Webpos\Model\Payment\Payment');
        $config = $this->helper->getConfig(array('secret_key'));
        $sortOrder = !empty($config['sort_order']) ? (int)$config['sort_order'] : 0;
        $paymentModel->setData($config);
        $paymentModel->setSortOrder($sortOrder);
        $paymentModel->setCode(StripeTerminalServiceInterface::CODE);
        $paymentModel->setInformation('');
        $paymentModel->setType('1');
        $paymentModel->setIsReferenceNumber(0);
        $paymentModel->setIsPayLater(0);
        $paymentModel->setMultiable(1);
        return $paymentModel;
    }
}