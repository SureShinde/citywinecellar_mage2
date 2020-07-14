<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\DataProvider;

use Amasty\Blog\Api\Data\PostInterface;
use Amasty\Blog\Model\ResourceModel\Posts\CollectionFactory;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class RelatedPostsProvider
 */
class RelatedPostsProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var PostInterface
     */
    private $post;

    /**
     * @var \Amasty\Blog\Model\Repository\PostRepository
     */
    private $postRepository;

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        \Amasty\Blog\Model\Repository\PostRepository $postRepository,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collectionFactory = $collectionFactory;
        $this->collection = $collectionFactory->create();
        $this->postRepository = $postRepository;
        $this->request = $request;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $collection = $this->getCollection();
        if ($this->getPost()) {
            $collection->addFieldToFilter(
                $collection->getIdFieldName(),
                ['nin' => [$this->getPost()->getPostId()]]
            );
        }

        $items = [];
        foreach ($collection->getItems() as $post) {
            $items[] = $this->fillData($post);
        }

        $data = [
            'totalRecords' => count($items),
            'items' => $items
        ];

        return $data;
    }

    /**
     * @param array $post
     *
     * @return array
     */
    protected function fillData(PostInterface $post)
    {
        return [
            'post_id'        => $post->getPostId(),
            'post_thumbnail' => $post->getListThumbnailSrc(),
            'title'          => $post->getTitle(),
            'url_key'        => $post->getUrlKey(),
            'status'         => $post->getStatus()
        ];
    }

    /**
     * Retrieve posp
     *
     * @return PostInterface|null
     */
    protected function getPost()
    {
        if (null !== $this->post) {
            return $this->post;
        }

        if (!($id = $this->request->getParam('post_id'))) {
            return null;
        }

        return $this->post = $this->postRepository->getById($id);
    }
}
