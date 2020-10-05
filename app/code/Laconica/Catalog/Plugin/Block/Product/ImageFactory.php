<?php

namespace Laconica\Catalog\Plugin\Block\Product;

use Magento\Framework\Exception\NoSuchEntityException;

class ImageFactory
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    private $productRepository;

    /**
     * @var \Laconica\Catalog\Helper\Config $configsHelper
     */
    private $configsHelper;

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Laconica\Catalog\Helper\Config $configsHelper
    ) {
        $this->productRepository = $productRepository;
        $this->configsHelper = $configsHelper;
    }

    public function afterCreate(
        \Magento\Catalog\Block\Product\ImageFactory $subject,
        \Magento\Catalog\Block\Product\Image $result
    ) {
        $productId = $result->getData('product_id');
        if (!$this->configsHelper->isAltReplaceEnabled() || !$productId) {
            return $result;
        }

        try {
            $product = $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            return $result;
        }

        $result->setData('label', $product->getName());

        return $result;
    }
}