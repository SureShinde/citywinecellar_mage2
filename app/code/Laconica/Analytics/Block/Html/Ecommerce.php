<?php

namespace Laconica\Analytics\Block\Html;

use Magento\Framework\View\Element\Template;

class Ecommerce extends Gtm
{
    /**
     * @var \Laconica\Analytics\Helper\Catalog $catalogHelper
     */
    protected $catalogHelper;

    /**
     * Ecommerce constructor.
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
        $category = $this->catalogHelper->getCurrentCategory();
        $productListBlock = $this->_layout->getBlock('category.products.list');

        if (!$category || !$productListBlock) {
            return $defaultData;
        }

        $categoryProducts = $productListBlock->getLoadedProductCollection();

        if (!$categoryProducts || $categoryProducts->getSize() <= 0) {
            return $defaultData;
        }

        $impressions = [];
        $counter = 0;
        foreach ($categoryProducts as $product) {
            if ($counter > $this->configHelper->getExpressionsLimit()) {
                break;
            }
            if (!$product || !$product->getId()) {
                continue;
            }
            $productCategory = $product->getCategory();
            array_push($impressions, [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'sku' => $product->getSku(),
                'price' => $this->configHelper->formatPrice($product->getFinalPrice()),
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
    protected function getProductInformation($defaultData)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->catalogHelper->getCurrentProduct();
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
                    'price' => $this->configHelper->formatPrice($product->getFinalPrice()),
                    'brand' => (string)$product->getAttributeText('producer'),
                    'category' => ($productCategory) ? $productCategory->getName() : ''
                ]
            ]
        ];
        return $data;
    }
}