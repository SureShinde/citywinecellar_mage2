<?php
/**
 * Created by Magestore Developer.
 * Date: 1/26/2016
 * Time: 4:09 PM
 * Set final price to product
 */

namespace Magestore\Rewardpoints\Observer\Webpos\Order;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderSaveAfter implements ObserverInterface
{

    /**
     * @var \Magestore\Rewardpoints\Helper\Action|\Magestore\Webpos\Helper\Data
     */
    protected $helper;
    /**
     * Store manager
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * SalesOrderSaveAfter constructor.
     * @param \Magestore\Webpos\Helper\Data $helper
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     */
    public function __construct(
        \Magestore\Rewardpoints\Helper\Action $helper,
        \Magento\Customer\Model\CustomerFactory $customerFactory

    )
    {
        $this->helper = $helper;
        $this->_customerFactory = $customerFactory;
    }

    /**
     * Set Final Price to product in product list
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer['order'];
        if ($order->getCustomerIsGuest() || !$order->getCustomerId()) {
            return $this;
        }

        // check order is placed by web pos pwa
        if (!$order->getPosLocationId()) {
            return $this;
        }

        // Process spending points for order
        if ($order->getRewardpointsSpent() > 0) {
            $this->helper->addTransaction('spending_order',
                $this->_customerFactory->create()->load($order->getCustomerId()),
                $order
            );
        }
        return $this;
    }
}