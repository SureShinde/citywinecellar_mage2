<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model;

use Amasty\Blog\Helper\Settings;
use Magento\Framework\Exception\NoSuchEntityException;

class UrlResolver
{
    /**
     * @var \Amasty\Blog\Api\PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var \Amasty\Blog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var \Amasty\Blog\Api\TagRepositoryInterface
     */
    private $tagRepository;

    /**
     * @var \Amasty\Blog\Api\AuthorRepositoryInterface
     */
    private $authorRepository;

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var int
     */
    private $storeId = 0;

    public function __construct(
        \Amasty\Blog\Api\PostRepositoryInterface $postRepository,
        \Amasty\Blog\Api\CategoryRepositoryInterface $categoryRepository,
        \Amasty\Blog\Api\TagRepositoryInterface $tagRepository,
        \Amasty\Blog\Api\AuthorRepositoryInterface $authorRepository,
        \Amasty\Blog\Helper\Settings $settings,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->tagRepository = $tagRepository;
        $this->categoryRepository = $categoryRepository;
        $this->postRepository = $postRepository;
        $this->authorRepository = $authorRepository;
        $this->settings = $settings;
        $this->storeManager = $storeManager;
    }

    /**
     * @param $id
     * @return string
     */
    public function getPostUrlById($id)
    {
        try {
            $url = $this->postRepository->getById($id)->getUrl();
        } catch (NoSuchEntityException $e) {
            $url = '';
        }

        return $url;
    }

    /**
     * @param $id
     * @param $page
     * @return string
     */
    public function getCategoryUrlById($id, $page = 1)
    {
        try {
            $url = $this->categoryRepository->getById($id)->getUrl($page);
        } catch (NoSuchEntityException $e) {
            $url = '';
        }

        return $url;
    }

    /**
     * @param $id
     * @param $page
     * @return string
     */
    public function getTagUrlById($id, $page = 1)
    {
        try {
            $url = $this->tagRepository->getById($id)->getUrl($page);
        } catch (NoSuchEntityException $e) {
            $url = '';
        }

        return $url;
    }

    /**
     * @param $id
     * @param $page
     * @return string
     */
    public function getAuthorUrlById($id, $page = 1)
    {
        try {
            $url = $this->authorRepository->getById($id)->getUrl($page);
        } catch (NoSuchEntityException $e) {
            $url = '';
        }

        return $url;
    }

    /**
     * @param int $page
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSearchPageUrl($page = 1)
    {
        return $this->getBaseUrl()
            . $this->settings->getSeoRoute()
            . '/' . \Amasty\Blog\Helper\Url::ROUTE_SEARCH
            . $this->getUrlPostfix($page);
    }

    /**
     * @param int $page
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBlogUrl($page = 1)
    {
        return $this->getBaseUrl() . $this->settings->getSeoRoute() . $this->getUrlPostfix($page);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getBaseUrl()
    {
        $storeId = $this->storeManager->getStore()->getId();

        return $this->storeManager->getStore($storeId)->getBaseUrl();
    }

    /**
     * @param int $page
     * @return string
     */
    private function getUrlPostfix($page = 1)
    {
        $postfix = $this->settings->getBlogPostfix();

        return $page > 1 ? "{$postfix}?p={$page}" : $postfix;
    }

    /**
     * @param $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;

        return $this;
    }
}
