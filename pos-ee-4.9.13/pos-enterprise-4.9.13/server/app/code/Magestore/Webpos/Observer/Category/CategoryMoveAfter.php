<?php

namespace Magestore\Webpos\Observer\Category;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class CategoryMoveAfter implements ObserverInterface {
    /**
     * @var \Magestore\Webpos\Api\Log\CategoryDeletedRepositoryInterface
     */
    protected $categoryDeletedRepository;
    /**
     * @var \Magestore\Webpos\Api\Data\Log\CategoryDeletedInterfaceFactory
     */
    protected $categoryDeletedInterfaceFactory;
    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Log\CategoryDeleted\CollectionFactory
     */
    protected $categoryDeletedCollection;
    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * CategoryMoveAfter constructor.
     * @param \Magestore\Webpos\Api\Log\CategoryDeletedRepositoryInterface $categoryDeletedRepository
     * @param \Magestore\Webpos\Api\Data\Log\CategoryDeletedInterfaceFactory $categoryDeletedInterfaceFactory
     * @param \Magestore\Webpos\Model\ResourceModel\Log\CategoryDeleted\CollectionFactory $categoryDeletedCollection
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        \Magestore\Webpos\Api\Log\CategoryDeletedRepositoryInterface $categoryDeletedRepository,
        \Magestore\Webpos\Api\Data\Log\CategoryDeletedInterfaceFactory $categoryDeletedInterfaceFactory,
        \Magestore\Webpos\Model\ResourceModel\Log\CategoryDeleted\CollectionFactory $categoryDeletedCollection,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
    ){
        $this->categoryDeletedRepository = $categoryDeletedRepository;
        $this->categoryDeletedInterfaceFactory = $categoryDeletedInterfaceFactory;
        $this->categoryDeletedCollection = $categoryDeletedCollection;
        $this->categoryRepository = $categoryRepository;
    }

    public function execute(EventObserver $observer)
    {
        $categoryId = $observer->getData('category_id');
        $prevParentId = $observer->getData('prev_parent_id');
        $parentId = $observer->getData('parent_id');

        $prevParent = $this->categoryRepository->get($prevParentId);
        $parent = $this->categoryRepository->get($parentId);

        $prevCatPath = explode('/', $prevParent->getPath());
        $rootPrevCategory = isset($prevCatPath[1]) ? $prevCatPath[1] : null;

        $catPath = explode('/', $parent->getPath());
        $rootCategory = isset($catPath[1]) ? $catPath[1] : null;

        if($rootPrevCategory != $rootCategory) {
            // add new category deleted on old root category
            if($rootPrevCategory) {
                $categoryDeleted = $this->categoryDeletedInterfaceFactory->create()
                    ->setCategoryId($categoryId)
                    ->setRootCategoryId($rootPrevCategory);
                $this->categoryDeletedRepository->save($categoryDeleted);
            }

            // delete all log category deleted on new root category
            $categoryDeletedCollection = $this->categoryDeletedCollection->create();
            $categoryDeletedCollection->addFieldToFilter('root_category_id', $rootCategory);
            $categoryDeletedCollection->addFieldToFilter('category_id', $categoryId);
            foreach ($categoryDeletedCollection as $catModel){
                $this->categoryDeletedRepository->deleteById($catModel->getId());
            }
            return;
        }
    }
}
