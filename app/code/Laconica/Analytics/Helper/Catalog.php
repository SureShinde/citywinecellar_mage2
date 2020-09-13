<?php

namespace Laconica\Analytics\Helper;

use Magento\Framework\Exception\NoSuchEntityException;

class Catalog
{
    /**
     * @var \Magento\Framework\App\RequestInterface $request
     */
    private $request;
    /**
     * @var \Magento\Catalog\Model\Session $catalogSession
     */
    private $catalogSession;
    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     */
    private $categoryRepository;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    private $productRepository;

    /**
     * Catalog constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    )
    {
        $this->request = $request;
        $this->catalogSession = $catalogSession;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface|null
     */
    public function getCurrentProduct()
    {
        $productId = (int)$this->request->getParam('id', false);
        $productSessionId = (int)$this->catalogSession->getLastViewedProductId();

        if ($productId && $productId === $productSessionId) {
            try {
                $product = $this->productRepository->getById($productId);
                return $product;
            } catch (NoSuchEntityException $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * @return \Magento\Catalog\Api\Data\CategoryInterface|null
     */
    public function getCurrentCategory()
    {
        $categoryId = (int)$this->request->getParam('id', false);
        $categorySessionId = (int)$this->catalogSession->getLastViewedCategoryId();

        if ($categoryId && $categoryId === $categorySessionId) {
            try {
                $category = $this->categoryRepository->get($categoryId);
                return $category;
            } catch (NoSuchEntityException $e) {
                return null;
            }
        }
        return null;
    }
}