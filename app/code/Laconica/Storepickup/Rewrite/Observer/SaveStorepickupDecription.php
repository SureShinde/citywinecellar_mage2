<?php

namespace Laconica\Storepickup\Rewrite\Observer;

use Magestore\Storepickup\Observer\SaveStorepickupDecription as ParentObserver;
use Symfony\Component\Config\Definition\Exception\Exception;
use Magestore\Storepickup\Model\ResourceModel\Orders\StorepickupStatus;

class SaveStorepickupDecription extends ParentObserver
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            /** @var \Magento\Sales\Model\Order $order */
            $order = $observer->getEvent()->getOrder();
            if($order->getShippingMethod()) {
                if ($order->getShippingMethod(true)->getCarrierCode() == "storepickup") {
                    $storePickupSession = $this->_checkoutSession->getData('storepickup_session') ?
                        $this->_checkoutSession->getData('storepickup_session') :
                        $this->_checkoutSession->getData('storepickup_shipping_description');
                    $this->_checkoutSession->unsetData('storepickup_session');
                    $this->_checkoutSession->unsetData('storepickup_shipping_description');
                    if ($storePickupSession) {
                        $new = $order->getShippingDescription();
                        $storeId = $storePickupSession['store_id'];
                        $collectionStore = $this->_storeCollection->create();
                        $store = $collectionStore->load($storeId, 'storepickup_id');
                        //set shipping desciption

                        // CUSTOM CODE
                        $pickupTime = '';
                        if (isset($storePickupSession['shipping_date']) && isset($storePickupSession['shipping_time'])) {
                            $pickupTime = $storePickupSession['shipping_date'].' '.$storePickupSession['shipping_time'];
                            $new .= '<br>' . __('Pickup date') . ' : ' . $storePickupSession['shipping_date'] . '<br>' . __('Pickup time') . ' : ' . $storePickupSession['shipping_time'];
                        } else {
                            $new .= '';
                        }
                        // END CUSTOM CODE

                        $order->setShippingDescription($new);

                        $order->setData('storepickup_id',$store->getId())
                            ->setData('storepickup_status',StorepickupStatus::STOREPICUP_PENDING)
                            ->setData('storepickup_time',$pickupTime);
                    }
                }
            }

        } catch (Exception $e) {

        }
    }
}