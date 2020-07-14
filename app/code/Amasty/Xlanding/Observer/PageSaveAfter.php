<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Observer;

use Amasty\Xlanding\Api\Data\PageInterface;
use Amasty\Xlanding\Api\PageRepositoryInterface;
use Amasty\Xlanding\Model\Page;
use \Magento\Catalog\Api\CategoryRepositoryInterface;

class PageSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    public function __construct(
        PageRepositoryInterface $pageRepository,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
    ) {
        $this->pageRepository = $pageRepository;
        $this->messageManager = $messageManager;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $page = $observer->getEvent()->getDataObject();
        if ($this->isChangeToDynamic($page)) {
            $catId = $page->getDynamicCategoryId();
            $category = $this->categoryRepository->get($catId);
            if ($category->getId()) {
                $category
                    ->setData(PageInterface::IS_CATEGORY_DYNAMIC, true)
                    ->setData(PageInterface::DYNAMIC_CATEGORY_PAGE_ID, $page->getId())
                    ->save();
                $this->messageManager->addNoticeMessage(
                    __(
                        'Display mode of %1 category (ID#%2) is set to Dynamic.',
                        $category->getName(),
                        $category->getId()
                    )
                );
            }
        } elseif ($this->isChangeFromDynamic($page)) {
            $catId = $page->getOrigData(PageInterface::DYNAMIC_CATEGORY_ID);
            if ($catId) {
                $category = $this->categoryRepository->get($catId);
                if ($category->getId()) {
                    $category
                        ->setData(PageInterface::IS_CATEGORY_DYNAMIC, false)
                        ->save();
                    $this->messageManager->addNoticeMessage(
                        __(
                            'Category %1 (ID#%2) is not in Dynamic mode anymore.',
                            $category->getName(),
                            $category->getId()
                        )
                    );
                }
            }
        }
    }

    /**
     * @param PageInterface $page
     * @return bool
     */
    private function isChangeToDynamic(PageInterface $page)
    {
        $isDynamic = $page->getIsActive() == Page::STATUS_DYNAMIC && $page->getDynamicCategoryId();
        $hasDataChanged = ($page->getOrigData(PageInterface::LANDING_IS_ACTIVE) != $page->getIsActive())
            || ($page->getOrigData(PageInterface::DYNAMIC_CATEGORY_ID) != $page->getDynamicCategoryId());
        return $isDynamic && $hasDataChanged;
    }

    /**
     * @param PageInterface $page
     * @return bool
     */
    private function isChangeFromDynamic(PageInterface $page)
    {
        return $page->getIsActive() != Page::STATUS_DYNAMIC
            && $page->getOrigData(PageInterface::LANDING_IS_ACTIVE) == Page::STATUS_DYNAMIC;
    }
}
