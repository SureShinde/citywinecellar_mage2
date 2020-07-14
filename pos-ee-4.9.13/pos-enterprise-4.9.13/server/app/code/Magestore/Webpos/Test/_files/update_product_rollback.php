<?php
/**
 *
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
//$productFactory = $objectManager->get(ProductInterfaceFactory::class);
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);
//$productRepository->cleanCache();


/*  rollback SKU-8  : product_type = simple to downloadable*/
$product = $productRepository->get(Product::SKU_8);
$product->setTypeId('downloadable');
$product->save($product);

/*  rollback SKU-13  : Visible_on_pos = false*/
$product = $productRepository->get(Product::SKU_13);
$product->setWebposVisible(1);
$product->save();

/*  rollback SKU-14  : Name */
$product = $productRepository->get(Product::SKU_14);
$product->setName(Product::NAME_14);
$product->save();

/*  rollback SKU-10  : Price  */
$product = $productRepository->get(Product::SKU_10);
$product->setPrice(10);
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
