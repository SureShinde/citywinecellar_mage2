<?php

namespace Laconica\Analytics\Block\Html;

use Magento\Framework\View\Element\Template;

class Ecommerce extends Gtm
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
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPushJson()
    {
        $defaultData = [
            'ecommerce' => [
                'currencyCode' => $this->_storeManager->getStore()->getCurrentCurrency()->getCode()
            ]
        ];
        $data = $this->getCategoryInformation($defaultData);
        $data = $this->getProductInformation($data);
        return json_encode($data);
    }

    /**
     * @param $defaultData
     * @return mixed
     */
    protected function getCategoryInformation($defaultData)
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = $this->registry->registry('current_category');
        if (!$category) {
            return $defaultData;
        }

        $productListBlock = $this->_layout->getBlock('category.products.list');

        if (empty($productListBlock)) {
            return $defaultData;
        }

        $categoryProducts = $productListBlock->getLoadedProductCollection();

        $impressions = [];
        $counter = 0;
        foreach ($categoryProducts as $product) {
            if($counter > $this->configHelper->getExpressionsLimit()){
                break;
            }
            if(!$product || !$product->getId()){
                continue;
            }
            $productCategory = $product->getCategory();
            array_push($impressions, [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'sku' => $product->getSku(),
                'price' => $this->configHelper->formatPrice($product->getPrice()),
                'category' => ($productCategory) ? $productCategory->getName() : '',
                'position' => $counter
            ]);
            $counter++;
        }
        $data['ecommerce'] = [
            'impressions' => $impressions
        ];
        return $data;
    }

    /**
     * @param $defaultData
     * @return array
     */
    protected function getProductInformation($defaultData){
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->registry->registry('current_product');
        if (!$product) {
            return $defaultData;
        }
        $productCategory = $product->getCategory();
        $data = [
            'ecommerce' => [
                'actionField' => [
                    'list' => ($productCategory) ? $productCategory->getName() : ''
                ],
                'detail' => [
                    'name' => $product->getName(),
                    'id' => $product->getId(),
                    'price' => $this->configHelper->formatPrice($product->getPrice()),
                    'brand' => (string)$product->getAttributeText('manufacturer'),
                    'category' => ($productCategory) ? $productCategory->getName() : ''
                ]
            ]
        ];
        return $data;
    }
}