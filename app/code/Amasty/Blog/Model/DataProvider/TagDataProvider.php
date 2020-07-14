<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\DataProvider;

use Amasty\Blog\Api\Data\TagInterface;
use Amasty\Blog\Controller\Adminhtml\Tags\Edit;
use Amasty\Blog\Model\Repository\TagRepository;
use Amasty\Blog\Model\Tag;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\App\Request\DataPersistorInterface;
use Amasty\Blog\Model\ResourceModel\Tag\CollectionFactory;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Amasty\Blog\Model\DataProvider\Traits\DataProviderTrait;
use Amasty\Blog\Model\BlogRegistry;

class TagDataProvider extends AbstractDataProvider
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
     * @var BlogRegistry
     */
    private $blogRegistry;

    /**
     * @var TagRepository
     */
    private $repository;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        DataPersistorInterface $dataPersistor,
        CollectionFactory $collectionFactory,
        PoolInterface $pool,
        BlogRegistry $blogRegistry,
        TagRepository $repository,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->pool = $pool;
        $this->blogRegistry = $blogRegistry;
        $this->repository = $repository;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getData()
    {
        $data = parent::getData();
        $storeId = $this->blogRegistry->registry(AbstractModifier::CURRENT_STORE_ID) ?: 0;
        $current = $this->blogRegistry->registry(Edit::CURRENT_AMASTY_BLOG_TAG);

        if ($current && $current->getId()) {
            $data[$current->getId()] = $current->getData();
            $this->addDataByStore($data, $storeId, $current->getId());
        }

        if ($savedData = $this->dataPersistor->get(Tag::PERSISTENT_NAME)) {
            $savedTagId = isset($savedData['tag_id']) ? $savedData['tag_id'] : null;
            $data[$savedTagId] = isset($data[$savedTagId])
                ? array_merge($data[$savedTagId], $savedData)
                : $savedData;
            $this->dataPersistor->clear(Tag::PERSISTENT_NAME);
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getFieldsByStore()
    {
        return TagInterface::FIELDS_BY_STORE;
    }
}
