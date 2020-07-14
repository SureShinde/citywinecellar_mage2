<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\DataProvider;

use Amasty\Blog\Model\Comments;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\App\Request\DataPersistorInterface;
use Amasty\Blog\Model\ResourceModel\Comments\CollectionFactory;
use Amasty\Blog\Controller\Adminhtml\Comments\Edit;

class CommentDataProvider extends AbstractDataProvider
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Amasty\Blog\Api\CommentRepositoryInterface
     */
    private $commentRepository;

    /**
     * @var \Amasty\Blog\Model\BlogRegistry
     */
    private $blogRegistry;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        DataPersistorInterface $dataPersistor,
        CollectionFactory $collectionFactory,
        \Amasty\Blog\Api\CommentRepositoryInterface $commentRepository,
        \Amasty\Blog\Model\BlogRegistry $blogRegistry,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->commentRepository = $commentRepository;
        $this->blogRegistry = $blogRegistry;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getData()
    {
        $data = parent::getData();

        if ($data['totalRecords'] > 0) {
            $commentId = (int)$data['items'][0]['comment_id'];
            $model = $this->commentRepository->getById($commentId);
            $data[$commentId] = $model->getData();
        }

        if ($savedData = $this->dataPersistor->get(Comments::PERSISTENT_NAME)) {
            $savedCommentId = isset($savedData['comment_id']) ? $savedData['comment_id'] : null;
            $data[$savedCommentId] = isset($data[$savedCommentId])
                ? array_merge($data[$savedCommentId], $savedData)
                : $savedData;
            $this->dataPersistor->clear(Comments::PERSISTENT_NAME);
        }

        return $data;
    }

    public function getMeta()
    {
        $meta = parent::getMeta();
        $meta['general']['children']['reply_to']['arguments']['data']['config']['default'] =
            $this->blogRegistry->registry(Edit::AMBLOG_COMMENT_REPLY_TO);

        return $meta;
    }
}
