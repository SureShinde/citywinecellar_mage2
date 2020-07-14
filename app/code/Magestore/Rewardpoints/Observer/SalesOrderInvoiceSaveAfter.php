<?php
/**
 * Created by Magestore Developer.
 * Date: 1/26/2016
 * Time: 4:09 PM
 * Set final price to product
 */

namespace Magestore\Rewardpoints\Observer;
use Magento\Framework\Event\ObserverInterface;

class SalesOrderInvoiceSaveAfter implements ObserverInterface
{

    /**
     * Store manager
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;
    /**
     * Helper Action
     *
     * @var \Magestore\Rewardpoints\Helper\Action
     */
    protected $_action;

    /**
     * SalesOrderInvoiceSaveAfter constructor.
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magestore\Rewardpoints\Helper\Action $action
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magestore\Rewardpoints\Helper\Action $action
    ){
        $this->_customerFactory = $customerFactory;
        $this->_action = $action;
    }
    /**
     * Set Final Price to product in product list
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $invoice = $observer['invoice'];
        $order   = $invoice->getOrder();
        if ($order->getCustomerIsGuest() || !$order->getCustomerId()
            || $invoice->getState() != \Magento\Sales\Model\Order\Invoice::STATE_PAID
            || !$order->getRewardpointsEarn()
        ) {
            return $this;
        }
        if (!$this->_action->getConfig(
            \Magestore\Rewardpoints\Helper\Calculation\Earning::XML_PATH_EARNING_ORDER_INVOICE,
            $order->getStoreId()
        )) {
            return $this;
        }
        $customer = $this->_customerFactory->create()->load($order->getCustomerId());
        if (!$customer->getId()) {
            return $this;
        }

        $this->_action->addTransaction(
            'earning_invoice', $customer, $invoice
        );

        return $this;
    }
}