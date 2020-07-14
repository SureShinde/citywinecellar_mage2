<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\DataProvider;

use Amasty\Blog\Model\Categories;
use Amasty\Blog\Api\Data\CategoryInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\App\Request\DataPersistorInterface;
use Amasty\Blog\Model\ResourceModel\Categories\CollectionFactory;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Amasty\Blog\Controller\Adminhtml\Categories\Edit;
use Amasty\Blog\Model\Repository\CategoriesRepository;
use Amasty\Blog\Model\BlogRegistry;
use Amasty\Blog\Model\DataProvider\Traits\DataProviderTrait;

class CategoryDataProvider extends AbstractDataProvider
{
    use DataProviderTrait;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @var CategoriesRepository
     */
    private $repository;

    /**
     * @var BlogRegistry
     */
    private $blogRegistry;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        DataPersistorInterface $dataPersistor,
        CollectionFactory $collectionFactory,
        CategoriesRepository $repository,
        PoolInterface $pool,
        \Amasty\Blog\Model\BlogRegistry $blogRegistry,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->repository = $repository;
        $this->dataPersistor = $dataPersistor;
        $this->pool = $pool;
        $this->blogRegistry = $blogRegistry;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getData()
    {
        $data = parent::getData();
        $storeId = $this->blogRegistry->registry(AbstractModifier::CURRENT_STORE_ID) ?: 0;
        $current = $this->blogRegistry->registry(Edit::CURRENT_AMASTY_BLOG_CATEGORY);

        if ($current && $current->getId()) {
            $data[$current->getId()] = $current->getData();
            $this->addDataByStore($data, $storeId, $current->getId());
        }

        if ($savedData = $this->dataPersistor->get(Categories::PERSISTENT_NAME)) {
            $savedCategoryId = isset($savedData['category_id']) ? $savedData['category_id'] : null;
            $data[$savedCategoryId] = isset($data[$savedCategoryId])
                ? array_merge($data[$savedCategoryId], $savedData)
                : $savedData;
            $this->dataPersistor->clear(Categories::PERSISTENT_NAME);
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getFieldsByStore()
    {
        return CategoryInterface::FIELDS_BY_STORE;
    }
}
