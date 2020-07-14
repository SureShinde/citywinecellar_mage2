<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\DataProvider;

use Amasty\Blog\Api\Data\AuthorInterface;
use Amasty\Blog\Controller\Adminhtml\Authors\Edit;
use Amasty\Blog\Model\Author;
use Amasty\Blog\Model\BlogRegistry;
use Amasty\Blog\Model\DataProvider\Traits\DataProviderTrait;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

class AuthorDataProvider extends AbstractDataProvider
{
    use DataProviderTrait;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Amasty\Blog\Api\AuthorRepositoryInterface
     */
    private $repository;

    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @var BlogRegistry
     */
    private $blogRegistry;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        DataPersistorInterface $dataPersistor,
        \Amasty\Blog\Api\AuthorRepositoryInterface $repository,
        PoolInterface $pool,
        BlogRegistry $blogRegistry,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->dataPersistor = $dataPersistor;
        $this->repository = $repository;
        $this->collection = $repository->getAuthorCollection();
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
        $current = $this->blogRegistry->registry(Edit::CURRENT_AMASTY_BLOG_AUTHOR);
        $data = $this->prepareData($current, $storeId, $data);

        if ($savedData = $this->dataPersistor->get(Author::PERSISTENT_NAME)) {
            $savedAuthorId = isset($savedData['author_id']) ? $savedData['author_id'] : null;
            $data[$savedAuthorId] = isset($data[$savedAuthorId])
                ? array_merge($data[$savedAuthorId], $savedData)
                : $savedData;
            $this->dataPersistor->clear(Author::PERSISTENT_NAME);
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getFieldsByStore()
    {
        return AuthorInterface::FIELDS_BY_STORE;
    }
}
