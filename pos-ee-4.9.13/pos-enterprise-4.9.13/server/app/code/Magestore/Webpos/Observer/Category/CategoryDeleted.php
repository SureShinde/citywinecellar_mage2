<?php

namespace Magestore\Webpos\Observer\Category;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class CategoryDeleted implements ObserverInterface {
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
     * CategoryDeleted constructor.
     * @param \Magestore\Webpos\Api\Log\CategoryDeletedRepositoryInterface $categoryDeletedRepository
     * @param \Magestore\Webpos\Api\Data\Log\CategoryDeletedInterfaceFactory $categoryDeletedInterfaceFactory
     * @param \Magestore\Webpos\Model\ResourceModel\Log\CategoryDeleted\CollectionFactory $categoryDeletedCollection
     */
    public function __construct(
        \Magestore\Webpos\Api\Log\CategoryDeletedRepositoryInterface $categoryDeletedRepository,
        \Magestore\Webpos\Api\Data\Log\CategoryDeletedInterfaceFactory $categoryDeletedInterfaceFactory,
        \Magestore\Webpos\Model\ResourceModel\Log\CategoryDeleted\CollectionFactory $categoryDeletedCollection
    ){
        $this->categoryDeletedRepository = $categoryDeletedRepository;
        $this->categoryDeletedInterfaceFactory = $categoryDeletedInterfaceFactory;
        $this->categoryDeletedCollection = $categoryDeletedCollection;
    }

    public function execute(EventObserver $observer)
    {
        $category = $observer->getCategory();
        $categoryId = $category->getId();
        $catPath = explode('/', $category->getPath());
        if(count($catPath) <= 1){
            return;
        }
        $rootCategoryId = $catPath[1];
        if ($rootCategoryId == $categoryId){
            // category which was deleted is root category
            $categoryDeletedCollection = $this->categoryDeletedCollection->create();
            $categoryDeletedCollection->addFieldToFilter('root_category_id', $rootCategoryId);
            foreach ($categoryDeletedCollection as $catModel){
                $this->categoryDeletedRepository->deleteById($catModel->getId());
            }
        } else {
            $categoryDeleted = $this->categoryDeletedInterfaceFactory->create()
                ->setCategoryId($categoryId)
                ->setRootCategoryId($rootCategoryId);
            $this->categoryDeletedRepository->save($categoryDeleted);

        }
    }
}
