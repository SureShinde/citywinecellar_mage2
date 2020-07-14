<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Observer;

use Amasty\Xlanding\Api\Data\PageInterface;
use Amasty\Xlanding\Model\ResourceModel\Page as PageResource;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

/**
 * Class CatalogCategorySaveAfter
 */
class CatalogCategorySaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    const DO_NOT_SYNC = 'am_xlanding_do_not_sync_category';

    /**
     * @var PageResource
     */
    private $pageResource;

    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    public function __construct(
        PageResource $pageResource,
        CategoryCollectionFactory $categoryCollectionFactory
    ) {
        $this->pageResource = $pageResource;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var \Magento\Catalog\Model\Category $category */
        $category = $observer->getEvent()->getDataObject();
        if ($category->hasDataChanges() && !$category->hasData(self::DO_NOT_SYNC)) {
            $this->syncLandingPages($category);
        }
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function syncLandingPages(\Magento\Catalog\Model\Category $category)
    {
        $pageId = $category->getData(PageInterface::DYNAMIC_CATEGORY_PAGE_ID);
        if ($pageId && $category->getData(PageInterface::IS_CATEGORY_DYNAMIC)) {

            $categoryToStaticCollection = $this->categoryCollectionFactory
                ->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', ['nin' => $category->getId()])
                ->addAttributeToFilter(PageInterface::DYNAMIC_CATEGORY_PAGE_ID, $pageId)
                ->addAttributeToFilter(PageInterface::IS_CATEGORY_DYNAMIC, true);
            foreach ($categoryToStaticCollection as $proceedCategory) {
                $proceedCategory->setData(PageInterface::IS_CATEGORY_DYNAMIC, false);
                $proceedCategory->setData(self::DO_NOT_SYNC, true);
            }

            $categoryToStaticCollection->save();
            $this->pageResource->syncDynamicPages($category->getId(), $pageId);
        }
    }
}
