<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PaymentOffline\Service;

/**
 * Class PaymentOfflineService
 * @package Magestore\PaymentOffline\Service
 */
class PaymentOfflineService
{
    /**
     * @var \Magestore\PaymentOffline\Model\ResourceModel\PaymentOffline\CollectionFactory
     */
    protected $paymentOfflineCollectionFactory;

    /**
     * PaymentOfflineService constructor.
     * @param \Magestore\PaymentOffline\Model\ResourceModel\PaymentOffline\CollectionFactory $paymentOfflineCollectionFactory
     */
    public function __construct(
        \Magestore\PaymentOffline\Model\ResourceModel\PaymentOffline\CollectionFactory $paymentOfflineCollectionFactory
    ){
        $this->paymentOfflineCollectionFactory = $paymentOfflineCollectionFactory;
    }

    /**
     * @param $paymentData
     */
    public function createPaymentOffline($paymentData) {
        $collection = $this->paymentOfflineCollectionFactory->create();
        $paymentOffline = $collection->addFieldToFilter('payment_code', $paymentData['payment_code'])
            ->setPageSize(1)
            ->setCurPage(1)
            ->getFirstItem();
        if ($paymentOffline->getId()) {
            if (!isset($paymentData['icon_path']) && !$paymentOffline->getIconPath()) {
                $paymentData['icon_path'] = 'default/Cash.svg';
            }
        }
        $paymentOffline->addData($paymentData);
        $paymentOffline->save();
    }

    /**
     * @return mixed
     */
    public function getExistedPaymentOffline()
    {
        return $this->paymentOfflineCollectionFactory->create();
    }

    /**
     * @param $paymentCode
     */
    public function removePayment($paymentCode)
    {
        $collection = $this->paymentOfflineCollectionFactory->create();
        $paymentOffline = $collection->addFieldToFilter('payment_code', $paymentCode)
            ->setPageSize(1)
            ->setCurPage(1)
            ->getFirstItem();
        if ($paymentOffline->getId()) {
            $paymentOffline->delete();
        }
    }

    /**
     * @return mixed
     */
    public function getAllPaymentOfflineAvailable()
    {
        $collection = $this->paymentOfflineCollectionFactory->create();
        $collection->addFieldToFilter(\Magestore\PaymentOffline\Model\PaymentOffline::ENABLE, \Magestore\PaymentOffline\Model\Source\Adminhtml\Enable::YES);
        return $collection;
    }
}