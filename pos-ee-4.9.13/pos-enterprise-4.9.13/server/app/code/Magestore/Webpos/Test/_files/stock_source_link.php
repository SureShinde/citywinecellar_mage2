<?php

/**
 * link source to stock
 */

use Magento\Framework\Api\DataObjectHelper;
use Magento\InventoryApi\Api\Data\StockSourceLinkInterface;
use Magento\InventoryApi\Api\Data\StockSourceLinkInterfaceFactory;
use Magento\InventoryApi\Api\StockSourceLinksSaveInterface;
use Magento\TestFramework\Helper\Bootstrap;

use Magento\Indexer\Model\Indexer;
use Magento\Indexer\Model\Indexer\Collection;

use Magestore\Webpos\Test\Constant\Stock;
use Magestore\Webpos\Test\Constant\Source;

/** @var DataObjectHelper $dataObjectHelper */
$dataObjectHelper = Bootstrap::getObjectManager()->get(DataObjectHelper::class);
/** @var StockSourceLinksSaveInterface $stockSourceLinksSave */
$stockSourceLinksSave = Bootstrap::getObjectManager()->get(StockSourceLinksSaveInterface::class);
/** @var StockSourceLinkInterfaceFactory $stockSourceLinkFactory */
$stockSourceLinkFactory = Bootstrap::getObjectManager()->get(StockSourceLinkInterfaceFactory::class);


$linksData = [
    [
        StockSourceLinkInterface::STOCK_ID => Stock::STOCK_ID,
        StockSourceLinkInterface::SOURCE_CODE => Source::SOURCE_CODE,
        StockSourceLinkInterface::PRIORITY => 1,
    ]
];

$links = [];
foreach ($linksData as $linkData) {
    /** @var StockSourceLinkInterface $link */
    $link = $stockSourceLinkFactory->create();
    $dataObjectHelper->populateWithArray($link, $linkData, StockSourceLinkInterface::class);
    $links[] = $link;
}
$stockSourceLinksSave->execute($links);

/* reindex */
$indexerCollection = Bootstrap::getObjectManager()->create(Collection::class);
$ids = $indexerCollection->getAllIds();
foreach ($ids as $id) {
    $indexerFactory = Bootstrap::getObjectManager()->create(Indexer::class);
    $idx = $indexerFactory->load($id);
    $idx->reindexAll($id); // this reindexes all
}