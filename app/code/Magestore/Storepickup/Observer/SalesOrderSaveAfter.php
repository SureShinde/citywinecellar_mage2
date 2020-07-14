<?php
/**
 *
 */
namespace Magestore\Storepickup\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class SalesOrderSaveAfter
 * @package Magestore\Storepickup\Observer
 */
class SalesOrderSaveAfter implements ObserverInterface
{
    /**
     * @var \Magestore\Storepickup\Helper\Email
     */
    protected $email;

    /**
     * SalesOrderSaveAfter constructor.
     * @param \Magestore\Storepickup\Helper\Email $email
     */
    public function __construct(
        \Magestore\Storepickup\Helper\Email $email
    ){
        $this->email = $email;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order->getState() != 'new' && $order->getData('storepickup_id')){
            $this->email->sendEmail($order, \Magestore\Storepickup\Helper\Email::TYPE_CHANGE_ORDER_STATUS_TO_STORE_OWNER);
        }
        return $this;
    }
}
