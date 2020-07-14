<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model;

use Amasty\Blog\Api\Data\ViewInterface;
use \Amasty\Blog\Model\ResourceModel\View as ViewResource;

/**
 * Class
 */
class View extends \Magento\Framework\Model\AbstractModel implements ViewInterface
{
    public function _construct()
    {
        parent::_construct();
        $this->_init(ViewResource::class);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->_getData(ViewInterface::VIEW_ID);
    }

    /**
     * @param int $viewId
     * @return $this|ViewInterface
     */
    public function setViewId($viewId)
    {
        $this->setData(ViewInterface::VIEW_ID, $viewId);

        return $this;
    }

    /**
     * @return int
     */
    public function getPostId()
    {
        return $this->_getData(ViewInterface::POST_ID);
    }

    /**
     * @param int $postId
     * @return $this|ViewInterface
     */
    public function setPostId($postId)
    {
        $this->setData(ViewInterface::POST_ID, $postId);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->_getData(ViewInterface::CUSTOMER_ID);
    }

    /**
     * @param int|null $customerId
     * @return $this|ViewInterface
     */
    public function setCustomerId($customerId)
    {
        $this->setData(ViewInterface::CUSTOMER_ID, $customerId);

        return $this;
    }

    /**
     * @returnstring
     */
    public function getSessionId()
    {
        return $this->_getData(ViewInterface::SESSION_ID);
    }

    /**
     * @param string $sessionId
     * @return $this|ViewInterface
     */
    public function setSessionId($sessionId)
    {
        $this->setData(ViewInterface::SESSION_ID, $sessionId);

        return $this;
    }

    /**
     * @return int
     */
    public function getRemoteAddr()
    {
        return $this->_getData(ViewInterface::REMOTE_ADDR);
    }

    /**
     * @param int $remoteAddr
     * @return $this|ViewInterface
     */
    public function setRemoteAddr($remoteAddr)
    {
        $this->setData(ViewInterface::REMOTE_ADDR, $remoteAddr);

        return $this;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->_getData(ViewInterface::STORE_ID);
    }

    /**
     * @param int $storeId
     * @return $this|ViewInterface
     */
    public function setStoreId($storeId)
    {
        $this->setData(ViewInterface::STORE_ID, $storeId);

        return $this;
    }

    /**
     * @returnstring
     */
    public function getCreatedAt()
    {
        return $this->_getData(ViewInterface::CREATED_AT);
    }

    /**
     * @param string $createdAt
     * @return $this|ViewInterface
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(ViewInterface::CREATED_AT, $createdAt);

        return $this;
    }

    /**
     * @returnstring|null
     */
    public function getRefererUrl()
    {
        return $this->_getData(ViewInterface::REFERER_URL);
    }

    /**
     * @param string|null $refererUrl
     * @return $this|ViewInterface
     */
    public function setRefererUrl($refererUrl)
    {
        $this->setData(ViewInterface::REFERER_URL, $refererUrl);

        return $this;
    }
}
