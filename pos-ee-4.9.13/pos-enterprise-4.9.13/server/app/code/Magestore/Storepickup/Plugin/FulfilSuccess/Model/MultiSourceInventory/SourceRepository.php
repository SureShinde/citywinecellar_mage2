<?php

namespace Magestore\Storepickup\Plugin\FulfilSuccess\Model\MultiSourceInventory;

class SourceRepository
{
    /**
     * @var \Magestore\Storepickup\Helper\Data
     */
    protected $storePickupHelper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magestore\Storepickup\Model\StoreFactory
     */
    protected $storePickupFactory;

    /**
     * SourceRepository constructor.
     * @param \Magestore\Storepickup\Helper\Data $storePickupHelper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magestore\Storepickup\Model\StoreFactory $storePickupFactory
     */
    public function __construct(
        \Magestore\Storepickup\Helper\Data $storePickupHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magestore\Storepickup\Model\StoreFactory $storePickupFactory
    )
    {
        $this->storePickupHelper = $storePickupHelper;
        $this->objectManager = $objectManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storePickupFactory = $storePickupFactory;
    }

    /**
     * @param \Magestore\FulfilSuccess\Model\MultiSourceInventory\SourceRepository $subject
     * @param \Closure $proceed
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\InventoryApi\Api\Data\SourceInterface[]
     */
    public function aroundGetAllowSourcesToPickFromOrder(
        $subject,
        \Closure $proceed,
        $order
    )
    {
        if ($order->getShippingMethod() !== 'storepickup_storepickup') {
            return $proceed($order);
        }
        if (!$this->storePickupHelper->isMSISourceEnable()) {
            return $proceed($order);
        }
        $storePickupId = $order->getStorepickupId();
        if (!$storePickupId) {
            return $proceed($order);
        }
        /** @var \Magestore\Storepickup\Model\Store $store */
        $store = $this->storePickupFactory->create()->load($storePickupId);
        if (!$store->getId() || !$store->getSourceCode()) {
            return $proceed($order);
        }
        $websiteId = $order->getStore()->getWebsiteId();
        /** @var \Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface $stockByWebsiteIdResolver */
        $stockByWebsiteIdResolver = $this->objectManager
            ->create('Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface');
        $stockId = (int)$stockByWebsiteIdResolver->execute((int)$websiteId)->getStockId();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('stock_id', $stockId)
            ->create();
        /** @var \Magento\InventoryApi\Api\GetStockSourceLinksInterface $getStockSourceLinks */
        $getStockSourceLinks = $this->objectManager->get('Magento\InventoryApi\Api\GetStockSourceLinksInterface');
        $searchResults = $getStockSourceLinks->execute($searchCriteria);
        $stockSourceLinks = $searchResults->getItems();
        /** @var \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository */
        $sourceRepository = $this->objectManager->get('Magento\InventoryApi\Api\SourceRepositoryInterface');
        $allowSources = [];
        foreach ($stockSourceLinks as $stockSourceLink) {
            try {
                $source = $sourceRepository->get($stockSourceLink->getSourceCode());
                if ($source->isEnabled() && $store->getSourceCode() == $source->getSourceCode()) {
                    $allowSources[$source->getSourceCode()] = $source;
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        return $allowSources;
    }
}