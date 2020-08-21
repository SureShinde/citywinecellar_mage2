<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PaymentOffline\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Observer GetPaymentAfter
 */
class GetPaymentAfter implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magestore\PaymentOffline\Service\PaymentOfflineService
     */
    protected $paymentOfflineService;

    /**
     * @var \Magestore\PaymentOffline\Helper\Data
     */
    protected $helperData;

    /**
     * GetPaymentAfter constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\PaymentOffline\Service\PaymentOfflineService $paymentOfflineService
     * @param \Magestore\PaymentOffline\Helper\Data $helperData
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\PaymentOffline\Service\PaymentOfflineService $paymentOfflineService,
        \Magestore\PaymentOffline\Helper\Data $helperData
    ) {
        $this->objectManager = $objectManager;
        $this->paymentOfflineService = $paymentOfflineService;
        $this->helperData = $helperData;
    }

    /**
     * Execute
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        /** @var \Magestore\WebposZippay\Helper\Data $zippayHelper */
        $payments = $observer->getData('payments');
        $paymentList = $payments->getList();
        $paymentOfflines = $this->paymentOfflineService->getAllPaymentOfflineAvailable();
        if (count($paymentOfflines)) {
            foreach ($paymentOfflines as $paymentOffline) {
                $paymentList[] = $this->addPaymentOffline($paymentOffline);
            }
        }
        $payments->setList($paymentList);
    }

    /**
     * Add Payment Offline
     *
     * @param \Magestore\PaymentOffline\Model\PaymentOffline $paymentOffline
     * @return mixed
     */
    public function addPaymentOffline($paymentOffline)
    {
        /** @var \Magestore\Webpos\Helper\Payment $paymentHelper */
        $sortOrder = $paymentOffline->getSortOrder();
        $sortOrder = $sortOrder ? (int)$sortOrder : 0;

        $paymentModel = $this->objectManager->create(\Magestore\Webpos\Model\Payment\Payment::class);
        $paymentModel->setCode($paymentOffline->getPaymentCode());
        $paymentModel->setTitle($paymentOffline->getTitle());
        $paymentModel->setInformation('');
        $paymentModel->setType(\Magestore\Webpos\Model\Source\Adminhtml\Payment::TYPE_OFFLINE_PAYMENT);
        $paymentModel->setIsDefault(0);
        $paymentModel->setIsReferenceNumber((int)($paymentOffline->getUseReferenceNumber()));
        $paymentModel->setIsPayLater((int)($paymentOffline->getUsePayLater()));
        $paymentModel->setMultiable(1);
        if ($paymentOffline->getUsePayLater()) {
            $paymentModel->setMultiable(0);
        }
        $paymentModel->setSortOrder($sortOrder);
        $paymentModel->setCanDue(true);
        if ($paymentOffline->getIconPath()) {
            $paymentModel->setIcon($this->helperData->getIconUrl($paymentOffline->getIconPath()));
        }
        return $paymentModel->getData();
    }
}
