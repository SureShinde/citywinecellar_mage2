<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Model\MultiSourceInventory;

/**
 * Class SourceItemRepository
 * @package Magestore\FulfilSuccess\Model\MultiSourceInventory
 */
class SourceRepository implements \Magestore\FulfilSuccess\Api\MultiSourceInventory\SourceRepositoryInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magestore\FulfilSuccess\Api\FulfilManagementInterface
     */
    protected $fulfilManagement;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * SourceRepository constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->objectManager = $objectManager;
        $this->fulfilManagement = $fulfilManagement;
        $this->orderRepository = $orderRepository;
        $this->eventManager = $eventManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Get allow sources to pick from order
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Magento\InventoryApi\Api\Data\SourceInterface[]
     */
    public function getAllowSourcesToPickFromOrder($order)
    {
        $allowSources = [];
        $websiteId = (int)$order->getStore()->getWebsiteId();
        /** @var \Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface $stockByWebsiteIdResolver */
        $stockByWebsiteIdResolver = $this->objectManager
            ->get('Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface');
        $stockId = (int)$stockByWebsiteIdResolver->execute((int)$websiteId)->getStockId();
        $eventData = new \Magento\Framework\DataObject(['stock_id' => $stockId, 'order' => $order]);
        $this->eventManager->dispatch(
            'fulfilsuccess_get_stock_id_from_order_to_prepare_fulfil',
            ['event_data' => $eventData]
        );
        $stockId = $eventData->getStockId();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('stock_id', $stockId)
            ->create();
        /** @var \Magento\InventoryApi\Api\GetStockSourceLinksInterface $getStockSourceLinks */
        $getStockSourceLinks = $this->objectManager->get('Magento\InventoryApi\Api\GetStockSourceLinksInterface');
        $searchResults = $getStockSourceLinks->execute($searchCriteria);
        $stockSourceLinks = $searchResults->getItems();
        /** @var \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository */
        $sourceRepository = $this->objectManager->get('Magento\InventoryApi\Api\SourceRepositoryInterface');
        foreach ($stockSourceLinks as $stockSourceLink) {
            try {
                $source = $sourceRepository->get($stockSourceLink->getSourceCode());
                if ($source->isEnabled()) {
                    $allowSources[$source->getSourceCode()] = $source;
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        $eventData = new \Magento\Framework\DataObject(['sources' => $allowSources, 'order' => $order]);
        $this->eventManager->dispatch(
            'fulfilsuccess_get_allow_sources_from_order_to_prepare_fulfil',
            ['event_data' => $eventData]
        );
        return $eventData->getSources();
    }
}
