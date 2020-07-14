<?php

/**
 * update Products
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

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);

//$productRepository->cleanCache();

/**  Update a product not include in products group that not satisfies 3 conditons */
/*  SKU-8 updated : product_type = simple*/
$product = $productRepository->get(Product::SKU_8);
$product->setTypeId(Type::TYPE_SIMPLE);
$product->save($product);

/**  Update 2 products that include in products group that satisfies 3 conditons */
/*  SKU-13 updated : Visible_on_pos = false*/
$product = $productRepository->get(Product::SKU_13);
$product->setWebposVisible(0);
$product->save();

/*  SKU-14 updated : Name */
$product = $productRepository->get(Product::SKU_14);
$product->setName(Product::UPDATED_NAME_SKU_14);
$product->save();


/**  Update a products that include in products group that not in Default Stock */
/*  SKU-10 updated : Price  */
$product = $productRepository->get(Product::SKU_10);
$product->setPrice(1000);
$product->save();

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
        ->addFilter(SourceItemInterface::SKU, [Product::SKU_10], 'in')
        ->addFilter(SourceItemInterface::SOURCE_CODE, $defaultSourceProvider->getCode())
        ->create();
    $sourceItems = $sourceItemRepository->getList($searchCriteria)->getItems();
    if (count($sourceItems)) {
        $sourceItemsDelete->execute($sourceItems);
    }
}
