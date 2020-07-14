<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Payment;
/**
 * class \Magestore\Webpos\Model\Payment\PaymnetRepository
 *
 * Methods:
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class PaymentRepository implements \Magestore\Webpos\Api\Payment\PaymentRepositoryInterface
{
    const EVENT_WEBPOS_GET_PAYMENT_AFTER = 'webpos_get_payment_after';
    /**
     * webpos payment source model
     *
     * @var \Magestore\Webpos\Model\Source\Adminhtml\Payment
     */
    protected $paymentModelSource;

    /**
     * webpos payment result interface
     *
     * @var \Magento\Framework\Api\SearchResults
     */
    protected $paymentResultInterface;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * PaymentRepository constructor.
     * @param \Magestore\Webpos\Model\Source\Adminhtml\Payment $paymentModelSource
     * @param \Magento\Framework\Api\SearchResults $paymentResultInterface
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        \Magestore\Webpos\Model\Source\Adminhtml\Payment $paymentModelSource,
        \Magento\Framework\Api\SearchResults $paymentResultInterface,
        \Magento\Framework\Event\ManagerInterface $eventManager
    )
    {
        $this->paymentModelSource = $paymentModelSource;
        $this->paymentResultInterface = $paymentResultInterface;
        $this->eventManager = $eventManager;
    }

    /**
     * @return \Magento\Framework\Api\SearchResults
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList()
    {
        $paymentList = $this->paymentModelSource->getPosPaymentMethods();
        $data = ['payments' => new \Magento\Framework\DataObject(['list' => $paymentList])];
        $this->eventManager->dispatch(
            self::EVENT_WEBPOS_GET_PAYMENT_AFTER,
            $data
        );
        $paymentList = $data['payments']->getList();
        $payments = $this->paymentResultInterface;
        $payments->setItems($paymentList);
        $payments->setTotalCount(count($paymentList));
        return $payments;
    }
}