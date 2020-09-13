<?php

namespace Laconica\Analytics\Block\Category;

use Magento\Framework\View\Element\Template;

class ProductList extends \Laconica\Analytics\Block\Html\Gtm
{

    /**
     * @var \Magento\Framework\Registry $registry
     */
    protected $registry;

    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Laconica\Analytics\Helper\Config $configHelper,
        array $data = [])
    {
        parent::__construct($context, $configHelper, $data);
        $this->registry = $registry;
    }

    /**
     * @return false|string
     */
    public function getPushJson()
    {
        $productList = $this->getProductList();
        if (!$productList) {
            return false;
        }
        $data = [
            'event' => 'productList',
            'ecommerce' => [
                'currencyCode' => $this->_storeManager->getStore()->getCurrentCurrency()->getCode(),
                'impressions' => $productList
            ]
        ];
        return json_encode($data);
    }

    protected function getProductList()
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = $this->registry->registry('current_category');
        if (!$category) {
            return [];
        }

        $productListBlock = $this->_layout->getBlock('category.products.list');

        if (empty($productListBlock)) {
            return [];
        }
        $categoryProducts = $productListBlock->getLoadedProductCollection();
        $products = [];
        foreach ($categoryProducts as $product) {
            if (!$product || !$product->getId()) {
                continue;
            }
            array_push($products, [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $this->configHelper->formatPrice($product->getFinalPrice())
            ]);
        }
        return $products;
    }
}