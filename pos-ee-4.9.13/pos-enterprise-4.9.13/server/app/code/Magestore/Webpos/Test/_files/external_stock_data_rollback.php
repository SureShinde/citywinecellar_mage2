<?php

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\InventoryApi\Api\SourceItemsDeleteInterface;
use Magento\TestFramework\Helper\Bootstrap;

use Magestore\Webpos\Test\Constant\Product;
use Magestore\Webpos\Test\Constant\Source;
use Magestore\Webpos\Test\Constant\Location;
use Magestore\Webpos\Api\Data\Location\LocationInterface;
use Magestore\Webpos\Model\ResourceModel\Location\Location\Collection;

/** @var SourceItemRepositoryInterface $sourceItemRepository */
$sourceItemRepository = Bootstrap::getObjectManager()->get(SourceItemRepositoryInterface::class);
/** @var SourceItemsDeleteInterface $sourceItemsDelete */
$sourceItemsDelete = Bootstrap::getObjectManager()->get(SourceItemsDeleteInterface::class);
/** @var SearchCriteriaBuilder $searchCriteriaBuilder */
$searchCriteriaBuilder = Bootstrap::getObjectManager()->get(SearchCriteriaBuilder::class);

$collectionLocation = Bootstrap::getObjectManager()->create(Collection::class)
    ->addFieldToFilter(LocationInterface::NAME, Location::NAME);
foreach($collectionLocation as $location){
    $location->delete();
}

$searchCriteria = $searchCriteriaBuilder->addFilter(SourceItemInterface::SKU, [Product::SKU_13], 'in')
    ->addFilter(SourceItemInterface::SOURCE_CODE, Source::SOURCE_CODE)
    ->create();

$sourceItems = $sourceItemRepository->getList($searchCriteria)->getItems();

/**
 * Tests which are wrapped with MySQL transaction clear all data by transaction rollback.
 * In that case there is "if" which checks that SKU-11, SKU-12  still exists in database.
 */
if (!empty($sourceItems)) {
    $sourceItemsDelete->execute($sourceItems);
}


