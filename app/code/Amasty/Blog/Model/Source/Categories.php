<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Amasty\Blog\Api\CategoryRepositoryInterface;

class Categories implements OptionSourceInterface
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $categories = [];
        $collection = $this->categoryRepository->getAllCategories();
        foreach ($collection as $category) {
            $categories[] = [
                'value' => $category->getCategoryId(),
                'label' => $category->getName()
            ];
        }

        return $categories;
    }
}
