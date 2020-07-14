<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\ReturnOrder\Option;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
/**
 * Class SupplierEnable
 * @package Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option
 */
class WarehouseEnable extends \Magestore\PurchaseOrderSuccess\Model\Option\AbstractOption
{
    /**
     * @var SourceRepositoryInterface
     */
    private $sourceRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param SourceRepositoryInterface $sourceRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        SourceRepositoryInterface $sourceRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->sourceRepository = $sourceRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Get Sources options
     * @return array
     */
    public function getSourcesOptions(){
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(SourceInterface::ENABLED, true)
            ->create();
        $sourcesSearchResult = $this->sourceRepository->getList($searchCriteria);
        $sourcesList = $sourcesSearchResult->getItems();

        $options = [' ' => __('Please select a source')];
        foreach ($sourcesList as $source){
            $options[$source->getSourceCode()] = $source->getName();
        }
        return $options;
    }

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public function getOptionHash()
    {
        return $this->getSourcesOptions();
    }
}