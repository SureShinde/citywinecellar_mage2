<?php
/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Magestore\Customercredit\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class \Magestore\Customercredit\Observer\SalesOrderPlaceAfter
 */
class SalesOrderPlaceAfter implements ObserverInterface
{
    /**
     * @var \Magestore\Customercredit\Model\TransactionFactory
     */
    protected $transaction;

    /**
     * @var \Magestore\Customercredit\Model\CustomercreditFactory
     */
    protected $customerCreditFactory;

    /**
     * @param \Magestore\Customercredit\Model\TransactionFactory $transaction
     * @param \Magestore\Customercredit\Model\CustomercreditFactory $customerCreditFactory
     */
    public function __construct(
        \Magestore\Customercredit\Model\TransactionFactory $transaction,
        \Magestore\Customercredit\Model\CustomercreditFactory $customerCreditFactory
    ) {
        $this->transaction = $transaction;
        $this->customerCreditFactory = $customerCreditFactory;
    }

    /**
     * Pre-dispatch admin action controller
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $customerId = $order->getCustomerId();
        $amount = $order->getBaseCustomercreditDiscount();
        if ($customerId && $amount) {
            $this->transaction->create()->addTransactionHistory(
                $customerId,
                \Magestore\Customercredit\Model\TransactionType::TYPE_CHECK_OUT_BY_CREDIT,
                __('check out by credit for order #') . $order->getIncrementId(),
                $order->getId(),
                -$amount
            );
            $this->customerCreditFactory->create()->changeCustomerCredit(-$amount, $customerId);
        }
    }
}
