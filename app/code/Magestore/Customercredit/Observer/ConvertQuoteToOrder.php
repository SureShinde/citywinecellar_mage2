<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

namespace Magestore\Customercredit\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class \Magestore\Customercredit\Observer\ConvertQuoteToOrder
 */
class ConvertQuoteToOrder implements ObserverInterface
{
    /**
     * @var \Magestore\Customercredit\Model\TransactionFactory
     */
    protected $_transaction;

    /**
     * @var \Magestore\Customercredit\Model\CustomercreditFactory
     */
    protected $_customercredit;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magestore\Customercredit\Model\TransactionFactory $transaction
     * @param \Magestore\Customercredit\Model\CustomercreditFactory $customercredit
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magestore\Customercredit\Model\TransactionFactory $transaction,
        \Magestore\Customercredit\Model\CustomercreditFactory $customercredit
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_transaction = $transaction;
        $this->_customercredit = $customercredit;
    }

    /**
     * Pre-dispatch admin action controller
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();
        $session = $this->_checkoutSession;
        if ($quote->getCustomercreditDiscount()) {
            $order->setCustomercreditDiscount($quote->getCustomercreditDiscount());
            $order->setBaseCustomercreditDiscount($quote->getBaseCustomercreditDiscount());
            $order->setBaseCustomercreditDiscountForShipping($quote->getBaseCustomercreditDiscountForShipping());
            $order->setCustomercreditDiscountForShipping($quote->getCustomercreditDiscountForShipping());
            $order->setMagestoreBaseDiscount($quote->getMagestoreBaseDiscount());
            $order->setMagestoreDiscount($quote->getMagestoreDiscount());
            $order->setMagestoreBaseDiscountForShipping($quote->getMagestoreBaseDiscountForShipping());
            $order->setMagestoreDiscountForShipping($quote->getMagestoreDiscountForShipping());
        }
        if ($session->getUseCustomerCredit()) {
            $session->setCustomerCreditAmount(null)
                ->setCreditdiscountAmount(null)
                ->setBaseCreditdiscountAmount(null)
                ->setUseCustomerCredit(false);
        } else {
            $session->setCustomerCreditAmount(null)
                ->setCreditdiscountAmount(null)
                ->setBaseCreditdiscountAmount(null);
        }
    }
}
