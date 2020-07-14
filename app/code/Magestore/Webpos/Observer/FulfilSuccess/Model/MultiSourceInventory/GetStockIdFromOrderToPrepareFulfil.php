<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Observer\FulfilSuccess\Model\MultiSourceInventory;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;


class GetStockIdFromOrderToPrepareFulfil implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magestore\Webpos\Api\WebposManagementInterface
     */
    protected $webposManagement;

    /**
     * @var \Magestore\Webpos\Api\Location\LocationRepositoryInterface
     */
    protected $locationRepository;

    /**
     * GetStockIdFromOrderToPrepareFulfil constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\Webpos\Api\WebposManagementInterface $webposManagement
     * @param \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\Webpos\Api\WebposManagementInterface $webposManagement,
        \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository
    )
    {
        $this->objectManager = $objectManager;
        $this->webposManagement = $webposManagement;
        $this->locationRepository = $locationRepository;
    }

    /**
     * Fulfilsuccess Get stock id from order to prepare fulfil
     *
     * @param EventObserver $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer)
    {
        if ($this->webposManagement->isMSIEnable()) {
            $eventData = $observer->getEventData();
            $order = $eventData->getOrder();
            $locationId = $order->getPosLocationId();
            if (!$locationId) {
                return $this;
            }
            try {
                $location = $this->locationRepository->getById($locationId);
                $stockId = $location->getStockId();
                if (!$stockId) {
                    return $this;
                }
                $eventData->setStockId($stockId);
                return $this;
            } catch (\Exception $e) {
                return $this;
            }
        }
    }

}