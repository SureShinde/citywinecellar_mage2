<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Ui\Component\Form;

use Amasty\Blog\Api\Data\CategoryInterface;
use Amasty\Blog\Model\BlogRegistry;
use Amasty\Blog\Model\DataProvider\AbstractModifier;
use Magento\Framework\Data\OptionSourceInterface;
use Amasty\Blog\Model\ResourceModel\Categories\CollectionFactory as CategoriesCollectionFactory;
use Amasty\Blog\Controller\Adminhtml\Categories\Edit as EditController;

class Categories implements OptionSourceInterface
{
    /**
     * @var CategoriesCollectionFactory
     */
    private $categoriesCollectionFactory;

    /**
     * @var BlogRegistry
     */
    private $blogRegistry;

    /**
     * @var array
     */
    private $categoriesTree;

    public function __construct(
        CategoriesCollectionFactory $categoriesCollectionFactory,
        BlogRegistry $blogRegistry
    ) {
        $this->categoriesCollectionFactory = $categoriesCollectionFactory;
        $this->blogRegistry = $blogRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getCategoriesTree();
    }

    /**
     * Retrieve categories tree
     *
     * @param bool $displayRoot
     * @return array
     */
    public function getCategoriesTree($displayRoot = false)
    {
        if ($this->categoriesTree === null) {
            $collection = $this->categoriesCollectionFactory->create();

            if ($category = $this->blogRegistry->registry(EditController::CURRENT_AMASTY_BLOG_CATEGORY)) {
                $excludePath = $category->getPath() . '/' . $category->getCategoryId();
                $collection
                    ->getSelect()
                    ->where('main_table.path NOT LIKE "?%"', $excludePath);
                $collection->addFieldToFilter('category_id', ['neq' => $category->getCategoryId()]);
            }

            $categoryById = [
                CategoryInterface::ROOT_CATEGORY_ID => [
                    'value' => CategoryInterface::ROOT_CATEGORY_ID,
                    'label' => __('Root Category'),
                    'is_active' => true
                ],
            ];

            $collection->addStoreFilter($this->blogRegistry->registry(AbstractModifier::CURRENT_STORE_ID) ?: 0);

            /**
             * @var CategoryInterface $category
             */
            foreach ($collection as $category) {
                foreach ([$category->getCategoryId(), (int)$category->getParentId()] as $categoryId) {
                    if (!isset($categoryById[$categoryId])) {
                        $categoryById[$categoryId] = ['value' => (string)$categoryId];
                    }
                }

                $categoryById[$category->getId()]['is_active'] = $category->getStatus();
                $categoryById[$category->getId()]['label'] = $category->getName();
                $categoryById[(int)$category->getParentId()]['optgroup'][] = &$categoryById[$category->getId()];
            }

            if ($displayRoot) {
                $this->categoriesTree = [$categoryById[CategoryInterface::ROOT_CATEGORY_ID]];
            } else {
                $this->categoriesTree = isset($categoryById[CategoryInterface::ROOT_CATEGORY_ID]['optgroup'])
                    ? $categoryById[CategoryInterface::ROOT_CATEGORY_ID]['optgroup']
                    : [];
            }
        }

        return $this->categoriesTree;
    }

    /**
     * @return CategoriesCollectionFactory
     */
    public function getCategoriesCollectionFactory()
    {
        return $this->categoriesCollectionFactory;
    }
}
