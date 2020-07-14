<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Plugin\Catalog\Block\Category;

use Magento\Catalog\Block\Category\View as CategoryViewBlock;
use Magento\Catalog\Model\Category;

class View
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry = null;

    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->coreRegistry = $registry;
    }

    /**
     * @param View $subject
     * @param Category $category
     * @return Category
     */
    public function afterGetCurrentCategory(CategoryViewBlock $subject, $category)
    {
        if ($page = $this->coreRegistry->registry('amasty_xlanding_page')) {
            $category->setData('url', $subject->getBaseUrl() . $page->getIdentifier());
        }

        return $category;
    }
}
