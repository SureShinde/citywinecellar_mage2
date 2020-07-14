<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Observer\FulfilSuccess\Sales;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;


class GetResourcePickingItemAfterPlaceOrder implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magestore\Webpos\Api\Location\LocationRepositoryInterface
     */
    protected $locationRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilderFactory;

    /**
     * @var \Magento\Framework\Api\SortOrderBuilderFactory
     */
    protected $sortOrderBuilderFactory;

    /**
     * GetResourcePickingItemAfterPlaceOrder constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository
    )
    {
        $this->objectManager = $objectManager;
        $this->locationRepository = $locationRepository;
    }

    /**
     * Fulfilsuccess Get Resource Picking Item After Place Order
     *
     * @param EventObserver $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer)
    {
        $pickingData = $observer->getPickingData();
        $posLocationId = $pickingData->getPosLocationId();
        if ($posLocationId) {
            try {
                $location = $this->locationRepository->getById($posLocationId);
                $stockId = $location->getStockId();
                if ($stockId) {
                    /** @var \Magento\InventoryApi\Api\GetSourcesAssignedToStockOrderedByPriorityInterface $getSourcesAssignedToStockOrderedByPriority */
                    $getSourcesAssignedToStockOrderedByPriority = $this->objectManager
                        ->get('Magento\InventoryApi\Api\GetSourcesAssignedToStockOrderedByPriorityInterface');
                    $assignedSourcesToStock = $getSourcesAssignedToStockOrderedByPriority->execute($stockId);
                    if (count($assignedSourcesToStock) > 0) {
                        foreach ($assignedSourcesToStock as $source) {
                            if ($source->isEnabled()) {
                                $pickingData->setResource($source->getSourceCode());
                                break;
                            }
                        }
                    }
                }
                return $this;
            } catch (\Exception $e) {
                return $this;
            }
        }
    }

}