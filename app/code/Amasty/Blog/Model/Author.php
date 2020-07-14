<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model;

use Amasty\Blog\Api\Data\AuthorInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Author extends AbstractModel implements IdentityInterface, AuthorInterface
{
    const PERSISTENT_NAME = 'amasty_blog_authors';

    const CACHE_TAG = 'amblog_author';

    public function _construct()
    {
        parent::_construct();
        $this->_cacheTag = self::CACHE_TAG;
        $this->_init(\Amasty\Blog\Model\ResourceModel\Author::class);
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return  self::ROUTE_AUTHOR;
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [
            \Amasty\Blog\Model\Lists::CACHE_TAG,
            self::CACHE_TAG . '_' . $this->getId()
        ];

        return $identities;
    }

    /**
     * @return int
     */
    public function getAuthorId()
    {
        return $this->_getData(AuthorInterface::AUTHOR_ID);
    }

    /**
     * @param int $authorId
     * @return $this|AuthorInterface
     */
    public function setAuthorId($authorId)
    {
        $this->setData(AuthorInterface::AUTHOR_ID, $authorId);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->_getData(AuthorInterface::NAME);
    }

    /**
     * @param string|null $name
     * @return $this|AuthorInterface
     */
    public function setName($name)
    {
        $this->setData(AuthorInterface::NAME, $name);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrlKey()
    {
        return $this->_getData(AuthorInterface::URL_KEY);
    }

    /**
     * @return string
     */
    public function getFacebookProfile()
    {
        return $this->getData(AuthorInterface::FACEBOOK_PROFILE);
    }

    /**
     * @param string $profileLink
     * @return $this
     */
    public function setFacebookProfile($profileLink)
    {
        $this->setData(AuthorInterface::FACEBOOK_PROFILE, $profileLink);

        return $this;
    }

    /**
     * @return string
     */
    public function getTwitterProfile()
    {
        return $this->getData(AuthorInterface::TWITTER_PROFILE);
    }

    /**
     * @param string $profileLink
     * @return $this
     */
    public function setTwitterProfile($profileLink)
    {
        $this->setData(AuthorInterface::TWITTER_PROFILE, $profileLink);

        return $this;
    }

    /**
     * @param string|null $urlKey
     * @return $this|AuthorInterface
     */
    public function setUrlKey($urlKey)
    {
        $this->setData(AuthorInterface::URL_KEY, $urlKey);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMetaTitle()
    {
        return $this->_getData(AuthorInterface::META_TITLE);
    }

    /**
     * @param string|null $metaTitle
     * @return $this|AuthorInterface
     */
    public function setMetaTitle($metaTitle)
    {
        $this->setData(AuthorInterface::META_TITLE, $metaTitle);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMetaTags()
    {
        return $this->_getData(AuthorInterface::META_TAGS);
    }

    /**
     * @param string|null $metaTags
     * @return $this|AuthorInterface
     */
    public function setMetaTags($metaTags)
    {
        $this->setData(AuthorInterface::META_TAGS, $metaTags);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMetaDescription()
    {
        return $this->_getData(AuthorInterface::META_DESCRIPTION);
    }

    /**
     * @param string|null $metaDescription
     * @return $this|AuthorInterface
     */
    public function setMetaDescription($metaDescription)
    {
        $this->setData(AuthorInterface::META_DESCRIPTION, $metaDescription);

        return $this;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->_getData(AuthorInterface::STORE_ID);
    }

    /**
     * @param int $storeId
     * @return $this|AuthorInterface
     */
    public function setStoreId($storeId)
    {
        $this->setData(AuthorInterface::STORE_ID, $storeId);

        return $this;
    }

    /**
     * @param null $name
     * @return \Amasty\Blog\Api\Data\AuthorInterface
     */
    public function prepapreUrlKey($name = null)
    {
        if ($name == null) {
            $name = $this->getName();
        }

        $urlKey = $this->encodeUrlKey($name);

        $this->setUrlKey($this->_getResource()->getUniqUrlKey($urlKey));
        return $this;
    }

    /**
     * @param string $name = null
     * @return string
     */
    private function encodeUrlKey($name = null)
    {
        if ($name == null) {
            $name = $this->getName();
        }

        $nameParts = explode(' ', $name);
        $nameParts = array_map(function ($namePart) {
            $namePart = strtolower($namePart);
            return preg_replace('/[^a-z0-9]/', '', $namePart);
        }, $nameParts);

        return implode('-', $nameParts);
    }
}
