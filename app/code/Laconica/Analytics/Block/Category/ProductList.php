<?php

namespace Laconica\Analytics\Block\Category;

use Magento\Framework\View\Element\Template;

class ProductList extends \Laconica\Analytics\Block\Html\Gtm
{

    /**
     * @var \Laconica\Analytics\Helper\Catalog $catalogHelper
     */
    protected $catalogHelper;

    /**
     * ProductList constructor.
     * @param Template\Context $context
     * @param \Laconica\Analytics\Helper\Catalog $catalogHelper
     * @param \Laconica\Analytics\Helper\Config $configHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Laconica\Analytics\Helper\Catalog $catalogHelper,
        \Laconica\Analytics\Helper\Config $configHelper,
        array $data = [])
    {
        parent::__construct($context, $configHelper, $data);
        $this->catalogHelper = $catalogHelper;
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
        try {
            $data = [
                'event' => 'productList',
                'ecommerce' => [
                    'currencyCode' => $this->_storeManager->getStore()->getCurrentCurrency()->getCode(),
                    'impressions' => $productList
                ]
            ];
            return json_encode($data);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return array
     */
    protected function getProductList()
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = $this->catalogHelper->getCurrentCategory();
        $productListBlock = $this->_layout->getBlock('category.products.list');
        $products = [];

        if (!$category || !$productListBlock) {
            return $products;
        }

        $categoryProducts = $productListBlock->getLoadedProductCollection();

        if (!$categoryProducts || $categoryProducts->getSize() <= 0) {
            return $products;
        }

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