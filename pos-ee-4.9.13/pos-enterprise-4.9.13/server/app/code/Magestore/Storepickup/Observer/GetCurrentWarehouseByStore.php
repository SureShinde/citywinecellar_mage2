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
use Exception;

class GetCurrentWarehouseByStore implements ObserverInterface
{

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @codeCoverageIgnore
     */
    protected $_checkoutSession;

    /**
     * @param \Magestore\Storepickup\Helper\Data
     * @codeCoverageIgnore
     */
    protected $helperData;

    /**
     * @var \Magestore\InventorySuccess\Model\Warehouse
     */
    protected $warehouse;

    /**
     * @var \Magestore\Storepickup\Model\StoreFactory
     */
    protected $storeFactory;

    /**
     * NewOrderWarehouse constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magestore\Storepickup\Helper\Data $helperData
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\Storepickup\Model\StoreFactory $storeFactory
     */
    public function __construct (
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magestore\Storepickup\Helper\Data $helperData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\Storepickup\Model\StoreFactory $storeFactory
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->helperData = $helperData;
        $this->storeManager = $storeManager;
        $this->_objectManager = $objectManager;
        $this->storeFactory = $storeFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        if ($this->_checkoutSession->getData('storepickup_session')||$this->_checkoutSession->getData('storepickup_session1')) {
            if($this->_checkoutSession->getData('storepickup_session')){
                $this->_checkoutSession->setData('storepickup_session1',$this->_checkoutSession->getData('storepickup_session'));
            }
            $storepickup_session = $this->_checkoutSession->getData('storepickup_session1', true);
            $storeId = $storepickup_session['store_id'];
            $storepickup = $this->storeFactory->create()->load($storeId);
            if($storepickup->getId() && $storepickup->getWarehouseId() && $observer->getEvent()->getWarehouse()->getId() != $storepickup->getWarehouseId()){
                $event           = $observer->getEvent();
                $this->warehouse = $this->_objectManager->create('Magestore\InventorySuccess\Model\Warehouse');
                $warehouse = $this->warehouse->load($storepickup->getWarehouseId());
                if($warehouse->getId()){
                    $event->getWarehouse()->load($storepickup->getWarehouseId());
                }
            }
        }
        return $this;
    }
}