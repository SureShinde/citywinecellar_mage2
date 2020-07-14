<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Ui\Component\Form\Categories;

use Amasty\Blog\Api\Data\CategoryInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Amasty\Blog\Model\ResourceModel\Categories\CollectionFactory as CategoriesCollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\Category as CategoryModel;

/**
 * Class
 */
class ParentCategory extends \Amasty\Blog\Ui\Component\Form\Categories implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getCategoriesTree(true);
    }
}
