<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\Repository;

use Amasty\Blog\Api\Data\PostInterface;
use Amasty\Blog\Api\PostRepositoryInterface;
use Amasty\Blog\Model\PostsFactory;
use Amasty\Blog\Model\ResourceModel\Posts as PostsResource;
use Amasty\Blog\Model\ResourceModel\Posts\CollectionFactory;
use Amasty\Blog\Model\Source\CategoryStatus;
use Amasty\Blog\Model\Source\PostStatus;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PostRepository implements PostRepositoryInterface
{
    /**
     * @var PostsFactory
     */
    private $postFactory;

    /**
     * @var PostsResource
     */
    private $postResource;

    /**
     * Model data storage
     *
     * @var array
     */
    private $posts;

    /**
     * @var CollectionFactory
     */
    private $postCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManagerInterface;

    /**
     * @var \Amasty\Blog\Helper\Settings
     */
    private $settings;

    public function __construct(
        PostsFactory $postFactory,
        PostsResource $postResource,
        CollectionFactory $postCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Amasty\Blog\Helper\Settings $settings
    ) {
        $this->postFactory = $postFactory;
        $this->postResource = $postResource;
        $this->postCollectionFactory = $postCollectionFactory;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->settings = $settings;
    }

    /**
     * @inheritdoc
     */
    public function save(PostInterface $post)
    {
        try {
            if ($post->getPostId()) {
                $post = $this->getById($post->getPostId())->addData($post->getData());
            } else {
                $post->setPostId(null);
            }
            $this->postResource->save($post);
            unset($this->posts[$post->getPostId()]);
        } catch (\Exception $e) {
            if ($post->getPostId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save posts with ID %1. Error: %2',
                        [$post->getPostId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new posts. Error: %1', $e->getMessage()));
        }

        return $post;
    }

    /**
     * @return \Amasty\Blog\Model\Posts
     */
    public function getPost()
    {
        return $this->postFactory->create();
    }

    /**
     * @param int $postId
     * @return PostInterface|mixed
     * @throws NoSuchEntityException
     */
    public function getById($postId)
    {
        if (!isset($this->posts[$postId])) {
            /** @var \Amasty\Blog\Model\Posts $posts */
            $posts = $this->postFactory->create();
            $this->postResource->load($posts, $postId);
            if (!$posts->getPostId()) {
                throw new NoSuchEntityException(__('Posts with specified ID "%1" not found.', $postId));
            }
            $this->posts[$postId] = $posts;
        }

        return $this->posts[$postId];
    }

    /**
     * @param $urlKey
     * @return \Magento\Framework\DataObject
     * @throws NoSuchEntityException
     */
    public function getByUrlKey($urlKey)
    {
        $collection = $this->postCollectionFactory->create();
        $collection->addFieldToFilter('url_key', $urlKey)
            ->addFieldToFilter(
                'status',
                [
                    'in' => [
                        PostStatus::STATUS_ENABLED,
                        PostStatus::STATUS_HIDDEN
                    ]
                ]
            )
            ->setUrlKeyIsNotNull();
        $this->addStoreFilter($collection);
        $collection->setLimit(1);

        return $collection->getFirstItem();
    }

    /**
     * @param $urlKey
     * @return \Magento\Framework\DataObject
     * @throws NoSuchEntityException
     */
    public function getByUrlKeyWithAllStatuses($urlKey)
    {
        $collection = $this->postCollectionFactory->create();
        $collection->addFieldToFilter('url_key', $urlKey)->setUrlKeyIsNotNull();
        $collection->setLimit(1);

        return $collection->getFirstItem();
    }

    /**
     * @param PostInterface $post
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(PostInterface $post)
    {
        try {
            $this->postResource->delete($post);
            unset($this->posts[$post->getPostId()]);
        } catch (\Exception $e) {
            if ($post->getPostId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove posts with ID %1. Error: %2',
                        [$post->getPostId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove posts. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @param int $postId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($postId)
    {
        $postsModel = $this->getById($postId);
        $this->delete($postsModel);

        return true;
    }

    /**
     * @param int $tagId
     * @return PostsResource\Collection
     */
    public function getTaggedPosts($tagId)
    {
        return $this->postCollectionFactory->create()->addTagFilter($tagId);
    }

    /**
     * @return PostsResource\Collection
     */
    public function getPostCollection()
    {
        return $this->postCollectionFactory->create();
    }

    /**
     * @param $page
     * @return PostsResource\Collection
     * @throws NoSuchEntityException
     */
    public function getPostsByPage($page)
    {
        return $this->getActivePosts()->setPageSize($this->settings->getPostsLimit())->setCurPage($page);
    }

    /**
     * @return PostsResource\Collection
     * @throws NoSuchEntityException
     */
    public function getRecentPosts()
    {
        $collection = $this->getActivePosts();
        $collection->setUrlKeyIsNotNull();
        $collection->setDateOrder();

        return $collection;
    }

    /**
     * @param PostsResource\Collection $collection
     * @param null $storeId
     * @throws NoSuchEntityException
     */
    private function addStoreFilter($collection, $storeId = null)
    {
        if (!$this->storeManagerInterface->isSingleStoreMode()) {
            $collection->addStoreFilter($storeId ?: $this->storeManagerInterface->getStore()->getId());
        }
    }

    /**
     * @param null $storeId
     * @return PostsResource\Collection
     * @throws NoSuchEntityException
     */
    public function getActivePosts($storeId = null)
    {
        $posts = $this->postCollectionFactory->create();

        $this->addStoreFilter($posts, $storeId);
        $posts->addFieldToFilter('status', PostStatus::STATUS_ENABLED);

        return $posts;
    }

    /**
     * @param null $storeId
     * @return PostsResource\Collection
     * @throws NoSuchEntityException
     */
    public function getFeaturedPosts($storeId = null)
    {
        $collection = $this->getActivePosts($storeId);
        $collection->addFieldToFilter(PostInterface::IS_FEATURED, 1)
            ->addFieldToFilter('list_thumbnail', ['neq' => 'NULL']);

        return $collection;
    }
}
