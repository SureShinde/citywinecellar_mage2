<?php
/**
 * delete stock
 */

use Magento\InventoryApi\Api\StockRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Framework\Exception\NoSuchEntityException;

use Magento\Indexer\Model\Indexer;
use Magento\Indexer\Model\Indexer\Collection;
use Magestore\Webpos\Test\Constant\Stock;

use Magestore\Webpos\Test\Constant\Location;
use Magestore\Webpos\Api\Data\Location\LocationInterface;
use Magestore\Webpos\Model\ResourceModel\Location\Location\Collection as CollectionLocation;

/** @var StockRepositoryInterface $stockRepository */
$stockRepository = Bootstrap::getObjectManager()->get(StockRepositoryInterface::class);
try {

    $collectionLocation = Bootstrap::getObjectManager()->create(CollectionLocation::class)
        ->addFieldToFilter(LocationInterface::NAME, Location::NAME);
    foreach($collectionLocation as $location){
        $location->delete();
    }

    $stockRepository->deleteById(Stock::STOCK_ID);
    /* reindex */
    $indexerCollection = Bootstrap::getObjectManager()->create(Collection::class);
    $ids = $indexerCollection->getAllIds();
    foreach ($ids as $id) {
        $indexerFactory = Bootstrap::getObjectManager()->create(Indexer::class);
        $idx = $indexerFactory->load($id);
        $idx->reindexAll($id); // this reindexes all
    }

} catch (NoSuchEntityException $e) {
    //Stock already removed
}
