<?php
/**
 * unlink stock-source
 */

use Magento\InventoryApi\Api\Data\StockSourceLinkInterface;
use Magento\InventoryApi\Api\Data\StockSourceLinkInterfaceFactory;
use Magento\InventoryApi\Api\StockSourceLinksDeleteInterface;
use Magento\TestFramework\Helper\Bootstrap;

/** @var StockSourceLinkInterfaceFactory $stockSourceLinkFactory */
$stockSourceLinkFactory = Bootstrap::getObjectManager()->get(StockSourceLinkInterfaceFactory::class);
/** @var StockSourceLinksDeleteInterface $stockSourceLinksDelete */
$stockSourceLinksDelete = Bootstrap::getObjectManager()->get(StockSourceLinksDeleteInterface::class);


use Magestore\Webpos\Test\Constant\Stock;
use Magestore\Webpos\Test\Constant\Source;

$linksData = [
    Stock::STOCK_ID => [Source::SOURCE_CODE]
];

$links = [];

foreach ($linksData as $stockID => $sourceCodes) {
    foreach ($sourceCodes as $sourceCode) {
        /** @var StockSourceLinkInterface $link */
        $link = $stockSourceLinkFactory->create();

        $link->setStockId($stockID);
        $link->setSourceCode($sourceCode);

        $links[] = $link;
    }
}

$stockSourceLinksDelete->execute($links);
