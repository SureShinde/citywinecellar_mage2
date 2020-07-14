<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model;

use Amasty\Blog\Api\Data\CommentInterface;

class Comments extends AbstractModel implements CommentInterface
{
    const PERSISTENT_NAME = 'amasty_blog_comments';

    /**
     * @var \Amasty\Blog\Model\Posts
     */
    private $post;

    /**
     * @var \Amasty\Blog\Model\UrlResolver
     */
    private $urlResolver;

    /**
     * @var \Amasty\Blog\Api\PostRepositoryInterface
     */
    private $postRepository;

    public function _construct()
    {
        parent::_construct();
        $this->urlResolver = $this->getData('url_resolver');
        $this->postRepository = $this->getData('post_repository');
        $this->_init(\Amasty\Blog\Model\ResourceModel\Comments::class);
    }

    /**
     * @return \Amasty\Blog\Api\Data\PostInterface|Posts
     */
    public function getPost()
    {
        try {
            if (!$this->post) {
                $post = $this->postRepository->getById($this->getPostId());
                $post->setStoreId($this->getStoreId());
                $this->post = $post;
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->_logger->critical($e->getMessage());
        }

        return $this->post;
    }

    /**
     * @return string
     */
    public function getPostTitle()
    {
        return $this->getPost() ? $this->getPost()->getTitle() : '';
    }

    /**
     * @param int $page
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getUrl($page = 1)
    {
        $postId = $this->getPost() ? $this->getPost()->getId() : null;
        $url = $this->urlResolver->setStoreId($this->getStoreId())->getPostUrlById($postId);

        return $url . '#am-blog-comment-' . $this->getId();
    }

    /**
     * @return int
     */
    public function getCommentId()
    {
        return $this->_getData(CommentInterface::COMMENT_ID);
    }

    /**
     * @param int $commentId
     *
     * @return $this
     */
    public function setCommentId($commentId)
    {
        $this->setData(CommentInterface::COMMENT_ID, $commentId);

        return $this;
    }

    /**
     * @return int
     */
    public function getPostId()
    {
        return $this->_getData(CommentInterface::POST_ID);
    }

    /**
     * @param int $postId
     *
     * @return $this
     */
    public function setPostId($postId)
    {
        $this->setData(CommentInterface::POST_ID, $postId);

        return $this;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->_getData(CommentInterface::STORE_ID);
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->setData(CommentInterface::STORE_ID, $storeId);

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->_getData(CommentInterface::STATUS);
    }

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->setData(CommentInterface::STATUS, $status);

        return $this;
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->_getData(CommentInterface::CUSTOMER_ID);
    }

    /**
     * @param int|null $customerId
     *
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        $this->setData(CommentInterface::CUSTOMER_ID, $customerId);

        return $this;
    }

    /**
     * @return int
     */
    public function getReplyTo()
    {
        return $this->_getData(CommentInterface::REPLY_TO);
    }

    /**
     * @param int|null $replyTo
     *
     * @return $this
     */
    public function setReplyTo($replyTo)
    {
        $this->setData(CommentInterface::REPLY_TO, $replyTo);

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->_getData(CommentInterface::MESSAGE);
    }

    /**
     * @param null|string $message
     *
     * @return $this
     */
    public function setMessage($message)
    {
        $this->setData(CommentInterface::MESSAGE, $message);

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_getData(CommentInterface::NAME);
    }

    /**
     * @param null|string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->setData(CommentInterface::NAME, $name);

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->_getData(CommentInterface::EMAIL);
    }

    /**
     * @param null|string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->setData(CommentInterface::EMAIL, $email);

        return $this;
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->_getData(CommentInterface::SESSION_ID);
    }

    /**
     * @param null|string $sessionId
     *
     * @return $this
     */
    public function setSessionId($sessionId)
    {
        $this->setData(CommentInterface::SESSION_ID, $sessionId);

        return $this;
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->_getData(CommentInterface::CREATED_AT);
    }

    /**
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(CommentInterface::CREATED_AT, $createdAt);

        return $this;
    }

    /**
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->_getData(CommentInterface::UPDATED_AT);
    }

    /**
     * @param string $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->setData(CommentInterface::UPDATED_AT, $updatedAt);

        return $this;
    }

    /**
     * @return array
     */
    public function getCacheTags()
    {
        $tags = parent::getCacheTags();
        if ($this->getSettingsHelper()->getCommentsAutoapprove()) {
            $postTags = $this->getPost()->getIdentities();
            if (!is_array($tags)) {
                return $postTags;
            }
            $tags = array_merge($tags, $postTags);
        }
        return $tags;
    }
}
