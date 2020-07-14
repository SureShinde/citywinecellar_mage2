<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Observer;

use Amasty\Xlanding\Api\Data\PageInterface;
use Amasty\Xlanding\Api\PageRepositoryInterface;
use \Magento\Catalog\Api\CategoryRepositoryInterface;

class PageDeleteAfter implements \Magento\Framework\Event\ObserverInterface
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
        $categoryId = $page->getOrigData(PageInterface::DYNAMIC_CATEGORY_ID);
        if ($categoryId) {
            try {
                $category = $this->categoryRepository->get($categoryId);
                $category
                    ->setData(PageInterface::IS_CATEGORY_DYNAMIC, false);
                $this->categoryRepository->save($category);
                $this->messageManager->addNoticeMessage(
                    __('Category %1 (ID#%2) is not in Dynamic mode anymore.',
                        $category->getName(), $category->getId())
                );
            }catch (\Exception $e) {
                //do nothing
            }
        }
    }
}
