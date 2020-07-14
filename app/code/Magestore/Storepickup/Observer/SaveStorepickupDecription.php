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
 * @package     Magestore_StorePickup
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Storepickup\Observer;

use Magento\Framework\Event\ObserverInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Magestore\Storepickup\Model\Store;
use Magestore\Storepickup\Model\ResourceModel\Orders\StorepickupStatus;

/**
 * Class GiftMessageConfigObserver
 *
 * @category Magestore
 * @package  Magestore_StorePickup
 * @module   StorePickup
 * @author   Magestore Developer
 */
class SaveStorepickupDecription implements ObserverInterface
{
    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @codeCoverageIgnore
     */
    protected $_checkoutSession;

    /**
     * @var \Magestore\Storepickup\Model\StoreFactory
     */
    protected $_storeCollection;
    /**
     * @var \Magento\Sales\Api\Data\OrderAddressInterface
     */
    protected $_orderAddressInterface;


    /**
     * SaveStorepickupDecription constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magestore\Storepickup\Model\StoreFactory $storeCollection
     * @param \Magento\Sales\Api\Data\OrderAddressInterface $orderAddressInterface
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magestore\Storepickup\Model\StoreFactory $storeCollection,
        \Magento\Sales\Api\Data\OrderAddressInterface $orderAddressInterface
    ){
        $this->_checkoutSession = $checkoutSession;
        $this->_storeCollection = $storeCollection;
        $this->_orderAddressInterface = $orderAddressInterface;
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

                        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                        $gooleAPI = $objectManager->create('Magestore\Storepickup\Helper\Data')->getGoogleApiKey();

                        $pickupTime = '';
                        if (isset($storePickupSession['shipping_date']) && isset($storePickupSession['shipping_time'])) {
                            $pickupTime = $storePickupSession['shipping_date'].' '.$storePickupSession['shipping_time'];
                            $new .= '<br>' . __('Pickup date') . ' : ' . $storePickupSession['shipping_date'] . '<br>' . __('Pickup time') . ' : ' . $storePickupSession['shipping_time'] . '<br><img src="http://maps.google.com/maps/api/staticmap?key='.$gooleAPI.'&center=' . $store->getData('latitude') . ',' . $store->getData('longitude') . '&zoom=15&size=200x200&markers=color:red|label:S|' . $store->getData('latitude') . ',' . $store->getData('longitude') . '&sensor=false"/>';
                        } else {
                            $new .= '<br><img src="http://maps.google.com/maps/api/staticmap?key='.$gooleAPI.'&center=' . $store->getData('latitude') . ',' . $store->getData('longitude') . '&zoom=15&size=200x200&markers=color:red|label:S|' . $store->getData('latitude') . ',' . $store->getData('longitude') . '&sensor=false"/>';
                        }
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
