<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Model\Repository;

use Amasty\Xlanding\Api\Data\PageInterface;
use Amasty\Xlanding\Api\PageRepositoryInterface;
use Amasty\Xlanding\Model\PageFactory;
use Amasty\Xlanding\Model\ResourceModel\Page;
use Amasty\Xlanding\Model\Page as LandingPage;
use Amasty\Xlanding\Model\ResourceModel\Page\CollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

class PageRepository implements PageRepositoryInterface
{
    /**
     * @var PageFactory
     */
    private $pageFactory;
    
    /**
     * @var Page
     */
    private $pageResource;
    
    /**
     * @var array
     */
    private $pages;
    
    /**
     * @var CollectionFactory
     */
    private $pageCollectionFactory;

    public function __construct(
        PageFactory $pageFactory,
        Page $pageResource,
        CollectionFactory $pageCollectionFactory
    ) {
        $this->pageFactory = $pageFactory;
        $this->pageResource = $pageResource;
        $this->pageCollectionFactory = $pageCollectionFactory;
    }

    /**
     * Save
     * @throws CouldNotSaveException
     * @param \Amasty\Xlanding\Api\Data\PageInterface $page
     * @return \Amasty\Xlanding\Api\Data\PageInterface
     */
    public function save(PageInterface $page)
    {
        try {
            $this->pageResource->save($page);
        } catch (\Exception $e) {
            if ($page->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save page with ID %1. Error: %2',
                        [$page->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new page. Error: %1', $e->getMessage()));
        }

        return $page;
    }

    /**
     * Get by id
     * @throws NoSuchEntityException
     * @param int $id
     * @return \Amasty\Xlanding\Api\Data\PageInterface
     */
    public function getById($id)
    {
        if (!isset($this->pages[$id])) {
            /** @var \Amasty\Xlanding\Model\Page $page */
            $page = $this->pageFactory->create();
            $this->pageResource->load($page, $id);
            if (!$page->getId()) {
                throw new NoSuchEntityException(__('page with specified ID "%1" not found.', $id));
            }
            $this->pages[$id] = $page;
        }

        return $this->pages[$id];
    }

    /**
     * Get by URL Key
     * @throws NoSuchEntityException
     * @param string $urlKey
     * @return \Amasty\Xlanding\Api\Data\PageInterface
     */
    public function getByUrlKey(string $urlKey)
    {
        /** @var \Amasty\Xlanding\Model\Page $page */
        $page = $this->pageFactory->create();
        $this->pageResource->load($page, $urlKey, \Amasty\Xlanding\Api\Data\PageInterface::LANDING_IDENTIFIER);
        if (!$page->getId()) {
            throw new NoSuchEntityException(__('Page with specified URL Key "%1" not found.', $urlKey));
        }

        return $page;
    }

    /**
     * Delete
     * @throws CouldNotDeleteException
     * @param \Amasty\Xlanding\Api\Data\PageInterface $page
     * @return bool true on success
     */
    public function delete(PageInterface $page)
    {
        try {
            $this->pageResource->delete($page);
            unset($this->pages[$page->getId()]);
        } catch (\Exception $e) {
            if ($page->getId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove page with ID %1. Error: %2',
                        [$page->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove page. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * Delete by id
     *
     * @param int $id
     * @return bool true on success
     */
    public function deleteById($id)
    {
        $pageModel = $this->getById($id);
        $this->delete($pageModel);

        return true;
    }

    /**
     * Lists
     *
     * @param $storeId = null
     * @return \Amasty\Xlanding\Api\Data\PageInterface[] Array of items.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function getList($storeId = null)
    {
        /** @var \Amasty\Xlanding\Model\ResourceModel\Page\Collection $pageCollection */
        $pageCollection = $this->pageCollectionFactory->create();
        $pageList = [];

        if ($storeId) {
            $pageCollection->addStoreFilter($storeId);
        }

        foreach ($pageCollection as $page) {
            $pageList[] = $page;
        }

        return $pageList;
    }

    /**
     * @param null $storeId = null
     * @return \Amasty\Xlanding\Api\Data\PageInterface[] Array of items.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function getEnabledList($storeId = null)
    {
        /** @var \Amasty\Xlanding\Model\ResourceModel\Page\Collection $pageCollection */
        $pageCollection = $this->pageCollectionFactory->create();
        $pageList = [];

        $pageCollection->addFieldToFilter('is_active', ['neq' => LandingPage::STATUS_DISABLED]);

        if ($storeId) {
            $pageCollection->addStoreFilter($storeId);
        }

        foreach ($pageCollection as $page) {
            $pageList[] = $page;
        }

        return $pageList;
    }

    /**
     * @inheritdoc
     */
    public function getCollection()
    {
        return $this->pageCollectionFactory->create();
    }
}
