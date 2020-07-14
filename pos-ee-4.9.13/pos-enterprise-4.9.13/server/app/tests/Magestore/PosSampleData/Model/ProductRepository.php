<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosSampleData\Model;
/**
 * Class Currency
 * @package Magestore\Webpos\Model\Config\Data
 */
class ProductRepository implements \Magestore\PosSampleData\Api\ProductRepositoryInterface
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * ProductRepository constructor.
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ){
        $this->productRepository = $productRepository;
    }

    /**
     * @inheritdoc
     */
    public function enableVisibleOnPos(\Magestore\PosSampleData\Api\Data\ProductInterface $product){
        $productModel = $this->productRepository->get($product->getSku());
        if(!$productModel->getId() || $productModel->getWebposVisible()) {
            return false;
        }
        $productModel->setWebposVisible(1);
        $this->productRepository->save($productModel);
    }
}