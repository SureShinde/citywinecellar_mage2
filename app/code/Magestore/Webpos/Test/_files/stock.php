<?php

/**
 * create stock
 */
use Magento\Framework\Api\DataObjectHelper;
use Magento\InventoryApi\Api\Data\StockInterface;
use Magento\InventoryApi\Api\Data\StockInterfaceFactory;
use Magento\InventoryApi\Api\StockRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;

use Magestore\Webpos\Test\Constant\Stock;

/** @var StockInterfaceFactory $stockFactory */
$stockFactory = Bootstrap::getObjectManager()->get(StockInterfaceFactory::class);
/** @var DataObjectHelper $dataObjectHelper */
$dataObjectHelper = Bootstrap::getObjectManager()->get(DataObjectHelper::class);
/** @var StockRepositoryInterface $stockRepository */
$stockRepository = Bootstrap::getObjectManager()->get(StockRepositoryInterface::class);

/** @var StockInterface $stock */
$stock = $stockFactory->create();
$dataObjectHelper->populateWithArray(
    $stock,
    [
        StockInterface::STOCK_ID => Stock::STOCK_ID,
        StockInterface::NAME => Stock::STOCK_NAME,
    ],
    StockInterface::class
);

$stockRepository->save($stock);
