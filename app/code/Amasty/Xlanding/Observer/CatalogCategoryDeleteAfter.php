<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Observer;

use Amasty\Xlanding\Api\Data\PageInterface;
use Amasty\Xlanding\Api\PageRepositoryInterface;
use Amasty\Xlanding\Model\ResourceModel\Page as PageResource;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

class CatalogCategoryDeleteAfter implements \Magento\Framework\Event\ObserverInterface
{
    const DO_NOT_SYNC = 'am_xlanding_do_not_sync_category';
    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var PageResource
     */
    private $pageResource;

    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    public function __construct(
        PageRepositoryInterface $pageRepository,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        PageResource $pageResource,
        CategoryCollectionFactory $categoryCollectionFactory
    ) {
        $this->pageRepository = $pageRepository;
        $this->messageManager = $messageManager;
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
        if ($category->getData(PageInterface::IS_CATEGORY_DYNAMIC)) {
            $this->syncLandingPages($category);
        }
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     */
    private function syncLandingPages(\Magento\Catalog\Model\Category $category)
    {
        $pageId = $category->getData(PageInterface::DYNAMIC_CATEGORY_PAGE_ID);
        if ($pageId && $category->getData(PageInterface::IS_CATEGORY_DYNAMIC)) {
            $this->pageResource->syncDynamicPages($category->getId());
        }
    }
}
