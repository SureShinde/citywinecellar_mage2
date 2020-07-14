<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Observer\InventorySuccess;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;


class CreateCreditmemoWarehouse implements ObserverInterface
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

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
        \Magento\Framework\App\RequestInterface $request,
        \Magestore\Webpos\Model\ResourceModel\Location\Location\CollectionFactory $locationFactory
    )
    {
        $this->_objectManager = $objectManager;
        $this->_coreRegistry = $coreRegistry;
        $this->logger = $logger;
        $this->request = $request;
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
        $warehouse = $observer->getEvent()->getWarehouse();

        // check if create new order
        if ($this->_coreRegistry->registry('create_creditmemo_webpos')) {
            /* if there is no posted warehouse_id, then get warehouse_id from location_id */
            /* get current location */
            $locationId = $this->_coreRegistry->registry('current_location_id');

            if (!$locationId) {
                return $this;
            }

            $warehouse->load($locationId);
            if ($warehouse && $warehouse->getId()) {
                $observer->getEvent()->setWarehouse($warehouse);
            }
            return $this;
        }
    }

}