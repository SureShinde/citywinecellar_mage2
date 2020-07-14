<?php

/**
 * create simple data
 * 15 products ( 11 in default source and  4 not in default source )
 */

use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Module\Manager;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\InventoryApi\Api\SourceItemsDeleteInterface;
use Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface;
use Magento\TestFramework\Helper\Bootstrap;

use Magestore\Webpos\Test\Constant\Product;

$objectManager = Bootstrap::getObjectManager();
/** @var ProductInterfaceFactory $productFactory */
$productFactory = $objectManager->get(ProductInterfaceFactory::class);
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);
$productRepository->cleanCache();


$stocksData = Product::stocksData();
$productsName = Product::productsName();
$productsSku = Product::productsSku();


/* type : simple  - enable : true ; visible on pos : false */
for ($i = 1; $i <= 3; $i++) {
    $product = $productFactory->create();
    $product->setTypeId(Type::TYPE_SIMPLE)
        ->setAttributeSetId(4)
        ->setName($productsName[$i])
        ->setSku($productsSku[$i])
        ->setStockData($stocksData[Product::OPERATOR_SKU.'-'.$i])
        ->setPrice(10)
        ->setWebposVisible(0)
        ->setStatus(Status::STATUS_ENABLED);
    $productRepository->save($product);
}


/* type : simple  - enable : false ; visible on pos : true */
for ($i = 4; $i <= 6; $i++) {
    $product = $productFactory->create();
    $product->setTypeId(Type::TYPE_SIMPLE)
        ->setAttributeSetId(4)
        ->setName($productsName[$i])
        ->setSku($productsSku[$i])
        ->setStockData($stocksData[Product::OPERATOR_SKU.'-'.$i])
        ->setPrice(10)
        ->setWebposVisible(1)
        ->setStatus(Status::STATUS_DISABLED);
    $productRepository->save($product);
}

/* type : downloadable  - enable : true ; visible on pos : true */
for ($i = 7; $i <= 8; $i++) {
    $product = $productFactory->create();
    $product->setTypeId('downloadable')
        ->setAttributeSetId(4)
        ->setName($productsName[$i])
        ->setSku($productsSku[$i])
        ->setStockData($stocksData[Product::OPERATOR_SKU.'-'.$i])
        ->setPrice(10)
        ->setWebposVisible(1)
        ->setStatus(Status::STATUS_ENABLED);
    $productRepository->save($product);
}

/* type : simple - enable : true : visible on pos : true || not in Default stock */
for ($i = 9; $i <= 12; $i++) {
    $product = $productFactory->create();
    $product->setTypeId(Type::TYPE_SIMPLE)
        ->setAttributeSetId(4)
        ->setName($productsName[$i])
        ->setSku($productsSku[$i])
        ->setStockData($stocksData[Product::OPERATOR_SKU.'-'.$i])
        ->setPrice(10)
        ->setWebposVisible(1)
        ->setStatus(Status::STATUS_ENABLED);
    $productRepository->save($product);
}


/* type : simple - enable : true : visible on pos : true || in Default Stock */
for ($i = 13; $i <= 15; $i++) {
    $product = $productFactory->create();
    $product->setTypeId(Type::TYPE_SIMPLE)
        ->setAttributeSetId(4)
        ->setName($productsName[$i])
        ->setSku($productsSku[$i])
        ->setStockData($stocksData[Product::OPERATOR_SKU.'-'.$i])
        ->setPrice(10)
        ->setWebposVisible(1)
        ->setStatus(Status::STATUS_ENABLED);
    $productRepository->save($product);
}

/** @var Manager $moduleManager */
$moduleManager = Bootstrap::getObjectManager()->get(Manager::class);
// soft dependency in tests because we don't have possibility replace fixture from different modules
if ($moduleManager->isEnabled('Magento_InventoryCatalog')) {
    /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
    $searchCriteriaBuilder = Bootstrap::getObjectManager()->get(SearchCriteriaBuilder::class);
    /** @var DefaultSourceProviderInterface $defaultSourceProvider */
    $defaultSourceProvider = $objectManager->get(DefaultSourceProviderInterface::class);
    /** @var SourceItemRepositoryInterface $sourceItemRepository */
    $sourceItemRepository = $objectManager->get(SourceItemRepositoryInterface::class);
    /** @var SourceItemsDeleteInterface $sourceItemsDelete */
    $sourceItemsDelete = $objectManager->get(SourceItemsDeleteInterface::class);

    // Unassign created product from default Source
    $searchCriteria = $searchCriteriaBuilder
        ->addFilter(SourceItemInterface::SKU, [Product::SKU_9, Product::SKU_10 , Product::SKU_11 , Product::SKU_12], 'in')
        ->addFilter(SourceItemInterface::SOURCE_CODE, $defaultSourceProvider->getCode())
        ->create();
    $sourceItems = $sourceItemRepository->getList($searchCriteria)->getItems();
    if (count($sourceItems)) {
        $sourceItemsDelete->execute($sourceItems);
    }
}
