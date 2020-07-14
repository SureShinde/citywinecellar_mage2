<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Observer\InventorySuccess;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magestore\Webpos\Model\Checkout\Data\ExtensionData;

class NewOrderWarehouse implements ObserverInterface
{

    /**
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Location\Location\CollectionFactory
     */
    protected $locationFactory;

    /**
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $coreRegistry,
        \Psr\Log\LoggerInterface $logger,
        \Magestore\Webpos\Model\ResourceModel\Location\Location\CollectionFactory $locationFactory
    )
    {
        $this->_objectManager = $objectManager;
        $this->_coreRegistry = $coreRegistry;
        $this->logger = $logger;
        $this->locationFactory = $locationFactory;
    }

    /**
     * Load linked Warehouse from Location of WebPOS Order
     *
     * @param EventObserver $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer)
    {
        if (!$this->_coreRegistry->registry('create_order_webpos')) {
            return $this;
        }
        $warehouse = $observer->getEvent()->getWarehouse();

        /* get current location */
        $locationId = $this->_coreRegistry->registry('current_location_id');

        if (!$locationId) {
            return $this;
        }

        $fulfillOnline = $this->_coreRegistry->registry('pos_fulfill_online');
        if ($fulfillOnline) {
            $primaryWarehouse = $this->locationFactory->create()
                ->addFieldToFilter('is_primary', 1)
                ->getFirstItem();
            if ($primaryWarehouse && $primaryWarehouse->getId()) {
                $warehouse = $primaryWarehouse;
                $observer->getEvent()->setWarehouse($warehouse);
                return $this;
            }
        }

        $warehouse->load($locationId);
        if ($warehouse && $warehouse->getId()) {
            $observer->getEvent()->setWarehouse($warehouse);
        }
        return $this;
    }

}