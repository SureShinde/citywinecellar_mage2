<?php
use Magento\Framework\Api\DataObjectHelper;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magento\TestFramework\Helper\Bootstrap;

use Magestore\Webpos\Test\Constant\Product;
use Magestore\Webpos\Test\Constant\Source;
use Magestore\Webpos\Test\Constant\Location;

use Magestore\Webpos\Model\Location\Location as LocationFactory;

/* create new Location */
$locationData = Location::LocationData();
Bootstrap::getObjectManager()->get(LocationFactory::class)->setData($locationData)->save();


/* create new stock and new source */
/* assgin item SKU-13 into source */

/** @var DataObjectHelper $dataObjectHelper */
$dataObjectHelper = Bootstrap::getObjectManager()->get(DataObjectHelper::class);
$sourceItemFactory = Bootstrap::getObjectManager()->get(SourceItemInterfaceFactory::class);
$sourceItemsSave = Bootstrap::getObjectManager()->get(SourceItemsSaveInterface::class);
$sourcesItemsData = [
    [
        SourceItemInterface::SOURCE_CODE => Source::SOURCE_CODE,
        SourceItemInterface::SKU => Product::SKU_13,
        SourceItemInterface::QUANTITY => 100,
        SourceItemInterface::STATUS => SourceItemInterface::STATUS_IN_STOCK,
    ]
];

$sourceItems = [];
foreach ($sourcesItemsData as $sourceItemData) {
    /** @var SourceItemInterface $source */
    $sourceItem = $sourceItemFactory->create();
    $dataObjectHelper->populateWithArray($sourceItem, $sourceItemData, SourceItemInterface::class);
    $sourceItems[] = $sourceItem;
}
$sourceItemsSave->execute($sourceItems);
