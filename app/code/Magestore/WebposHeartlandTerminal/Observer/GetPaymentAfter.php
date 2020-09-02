<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposHeartlandTerminal\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magestore\WebposHeartlandTerminal\Api\TerminalServiceInterface;

/**
 * Class GetPaymentAfter
 *
 * @package Magestore\WebposHeartlandTerminal\Observer
 */
class GetPaymentAfter implements ObserverInterface
{
    /**
     * @var \Magestore\WebposHeartlandTerminal\Helper\Data
     */
    protected $helper;
    /**
     * GetPaymentAfter constructor.
     *
     * @param \Magestore\WebposHeartlandTerminal\Helper\Data $helper
     */
    public function __construct(\Magestore\WebposHeartlandTerminal\Helper\Data $helper)
    {
        $this->helper = $helper;
    }
    /**
     * Execute
     *
     * @param EventObserver $observer
     * @return void
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
     * Add
     *
     * @return \Magestore\Webpos\Model\Payment\Payment
     */
    public function add()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $paymentModel = $objectManager->create(\Magestore\Webpos\Model\Payment\Payment::class);
        $config = $this->helper->getConfig();
        $sortOrder = !empty($config['sort_order']) ? (int)$config['sort_order'] : 0;
        $paymentModel->setData($config);
        $paymentModel->setSortOrder($sortOrder);
        $paymentModel->setCode(TerminalServiceInterface::CODE);
        $paymentModel->setInformation('');
        $paymentModel->setType('1');
        $paymentModel->setIsReferenceNumber(0);
        $paymentModel->setIsPayLater(0);
        $paymentModel->setMultiable(1);
        return $paymentModel;
    }
}
