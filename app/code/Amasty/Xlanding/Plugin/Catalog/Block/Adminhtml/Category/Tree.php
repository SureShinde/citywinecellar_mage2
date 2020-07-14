<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Plugin\Catalog\Block\Adminhtml\Category;

use \Magento\Catalog\Block\Adminhtml\Category\Tree as CatalogTree;
use Amasty\Xlanding\Api\Data\PageInterface;

class Tree
{
    private $categoryCollection;

    public function __construct(\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory)
    {
        $this->categoryCollection = $collectionFactory->create()
            ->addAttributeToFilter(PageInterface::IS_CATEGORY_DYNAMIC, 1)
            ->addAttributeToFilter(PageInterface::DYNAMIC_CATEGORY_PAGE_ID, ['notnull' => true]);
    }


    public function beforeBuildNodeName(CatalogTree $tree, $node)
    {
        if($this->categoryCollection->getItemById($node->getData('entity_id'))) {
            $node->setData('product_count', __('Dynamic'));
        }
        return [$node];
    }
}