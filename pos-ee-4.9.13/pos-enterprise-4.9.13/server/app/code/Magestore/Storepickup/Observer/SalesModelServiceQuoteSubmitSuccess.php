<?php
namespace Magestore\Storepickup\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesModelServiceQuoteSubmitSuccess implements ObserverInterface
{
    /**
     * @var \Magestore\Storepickup\Helper\Email
     */
    protected $email;

    public function __construct(
        \Magestore\Storepickup\Helper\Email $email
    ){
        $this->email = $email;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            /** @var \Magento\Sales\Model\Order $order */
            $order = $observer->getEvent()->getOrder();
            if($order->getData('storepickup_id')) {
                $this->email->sendEmail($order, \Magestore\Storepickup\Helper\Email::TYPE_SEND_NEW_ORDER_TO_ADMIN);
                $this->email->sendEmail($order, \Magestore\Storepickup\Helper\Email::TYPE_SEND_NEW_ORDER_TO_STORE_OWNER);
            }
        } catch (\Exception $e) {

        }
    }
}