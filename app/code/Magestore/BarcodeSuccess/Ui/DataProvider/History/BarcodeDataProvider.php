<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Ui\DataProvider\History;

use Magestore\BarcodeSuccess\Ui\DataProvider\Barcode\DataProvider as ParentBarcodeDataProvider;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\ReportingInterface;

/**
 * Class BarcodeDataProvider
 *
 * Used to create barcode data provider
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BarcodeDataProvider extends ParentBarcodeDataProvider
{
    protected $searchCriteria;
    protected $searchCriteriaBuilder;
    protected $reporting;

    /**
     * BarcodeDataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magestore\BarcodeSuccess\Helper\Data $helper
     * @param \Magestore\BarcodeSuccess\Model\Locator\LocatorInterface $locator
     * @param \Magestore\BarcodeSuccess\Model\ResourceModel\Barcode\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\Api\Search\ReportingInterface $reporting,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magestore\BarcodeSuccess\Helper\Data $helper,
        \Magestore\BarcodeSuccess\Model\Locator\LocatorInterface $locator,
        \Magestore\BarcodeSuccess\Model\ResourceModel\Barcode\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $urlBuilder,
            $helper,
            $locator,
            $collectionFactory,
            $productFactory,
            $imageHelper,
            $stockRegistry,
            $meta,
            $data
        );
        $this->reporting = $reporting;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $historyId = $this->locator->getCurrentBarcodeHistory();
        if ($historyId !== false) {
            $this->collection->addFieldToFilter('history_id', $historyId);
        }
    }

    /**
     * Get search criteria
     *
     * @return \Magento\Framework\Api\Search\SearchCriteria
     */
    public function getSearchCriteria()
    {
        if (!$this->searchCriteria) {
            $this->searchCriteria = $this->searchCriteriaBuilder->create();
            $this->searchCriteria->setRequestName($this->name);
            $historyId = $this->locator->getCurrentBarcodeHistory();
            if ($historyId) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $filter = $objectManager->create(\Magento\Framework\Api\Filter::class);
                $filterGroup = $objectManager->create(\Magento\Framework\Api\Search\FilterGroup::class);
                $filter->setField('history_id');
                $filter->setValue($this->locator->getCurrentBarcodeHistory())->setConditionType('eq');
                $filterGroup->setFilters([$filter]);
                $this->searchCriteria->setFilterGroups([$filterGroup]);
            }
        }
        return $this->searchCriteria;
    }
}
