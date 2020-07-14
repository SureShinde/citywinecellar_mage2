<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Observer;

use Amasty\Xlanding\Api\Data\PageInterface;

/**
 * Class CatalogCategorySaveBefore
 */
class CatalogCategorySaveBefore implements \Magento\Framework\Event\ObserverInterface
{
    const DO_NOT_SYNC = 'am_xlanding_do_not_sync_category';

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    public function __construct(\Magento\Framework\Module\Manager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    /**
     * @inheritdoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var \Magento\Catalog\Model\Category $category */
        $category = $observer->getEvent()->getDataObject();
        if ($category->hasDataChanges() && !$category->hasData(self::DO_NOT_SYNC)) {
            if ($category->getData(PageInterface::IS_CATEGORY_DYNAMIC)) {
                try {
                    $pageId = $category->getData(PageInterface::DYNAMIC_CATEGORY_PAGE_ID);
                    if (!$pageId) {
                        $this->makeCategoryStatic($category);
                    }
                } catch (\Exception $e) {
                    $this->makeCategoryStatic($category);
                }
            }
        }
        $origDynamicData = $category->getOrigData(PageInterface::IS_CATEGORY_DYNAMIC);
        $dynamicData = $category->getData(PageInterface::IS_CATEGORY_DYNAMIC);
        $origDynamicPageId = $category->getOrigData(PageInterface::DYNAMIC_CATEGORY_PAGE_ID);
        $dynamicPageId = $category->getData(PageInterface::DYNAMIC_CATEGORY_PAGE_ID);
        if ($origDynamicData != $dynamicData || $origDynamicPageId != $dynamicPageId) {
            $category->setAffectedProductIds([0]);
        }
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @return \Magento\Catalog\Model\Category
     */
    private function makeCategoryStatic(\Magento\Catalog\Model\Category $category)
    {
        if (!$this->moduleManager->isEnabled('Amasty_VisualMerch')) {
            return $category->setData(PageInterface::IS_CATEGORY_DYNAMIC, false);
        }
        return $category;
    }
}
