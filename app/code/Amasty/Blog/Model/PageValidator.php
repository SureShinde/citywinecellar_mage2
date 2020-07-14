<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model;

use Amasty\Blog\Api\Data\PostInterface;
use Amasty\Blog\Helper\Settings;
use Amasty\Blog\Api\Data\CategoryInterface;
use Amasty\Blog\Api\Data\TagInterface;
use Amasty\Blog\Api\Data\AuthorInterface;
use Magento\Framework\App\RequestInterface;

class PageValidator
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
     * @var \Magento\Theme\Block\Html\Pager
     */
    private $pager;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var UrlResolver
     */
    private $urlResolver;

    public function __construct(
        \Amasty\Blog\Api\PostRepositoryInterface $postRepository,
        \Amasty\Blog\Api\CategoryRepositoryInterface $categoryRepository,
        \Amasty\Blog\Api\TagRepositoryInterface $tagRepository,
        \Amasty\Blog\Api\AuthorRepositoryInterface $authorRepository,
        \Amasty\Blog\Helper\Settings $settings,
        \Magento\Theme\Block\Html\Pager $pager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        RequestInterface $request,
        UrlResolver $urlResolver
    ) {
        $this->tagRepository = $tagRepository;
        $this->categoryRepository = $categoryRepository;
        $this->postRepository = $postRepository;
        $this->authorRepository = $authorRepository;
        $this->settings = $settings;
        $this->pager = $pager;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->urlResolver = $urlResolver;
    }

    /**
     * @param $identifier
     * @return int|bool
     */
    public function getPostId($identifier)
    {
        $clean = $this->cleanUrl($identifier);
        $post = $this->postRepository->getByUrlKey($clean);

        return $post->getId() ?: false;
    }

    /**
     * @param $identifier
     * @param int $page
     * @return bool
     */
    public function getCategoryId($identifier, $page = 1)
    {
        $categoryUrlKey = $this->getUrlKey($identifier, $page, CategoryInterface::ROUTE_CATEGORY);
        $category = $this->categoryRepository->getByUrlKey($categoryUrlKey);

        return $category->getId() ?: false;
    }

    /**
     * @param $identifier
     * @param int $page
     * @return bool
     */
    public function getTagId($identifier, $page = 1)
    {
        $tagUrlKey = $this->getUrlKey($identifier, $page, TagInterface::ROUTE_TAG);
        $tag = $this->tagRepository->getByUrlKey($tagUrlKey);

        return $tag->getId() ?: false;
    }

    /**
     * @param $identifier
     * @param int $page
     * @return bool
     */
    public function getAuthorId($identifier, $page = 1)
    {
        $authorUrlKey = $this->getUrlKey($identifier, $page, AuthorInterface::ROUTE_AUTHOR);
        $author = $this->authorRepository->getByUrlKey($authorUrlKey);

        return $author->getAuthorId();
    }

    /**
     * @param $identifier
     * @param int $page
     * @return bool
     */
    public function isIndexRequest($identifier, $page = 1)
    {
        $identifier = str_replace(
            [$this->getOldUrlPostfix($page), $this->getUrlPostfix(), '/', '.html', '.htm'],
            '',
            stristr($identifier, '?', true) ?: $identifier
        );

        return $identifier == $this->settings->getSeoRoute()
            || stristr($identifier, '/', true) == $this->settings->getSeoRoute(); // for back compatibility
    }

    /**
     * @param $identifier
     * @param int $page
     * @return bool
     */
    public function getIsSearchRequest($identifier, $page = 1)
    {
        $clearUrl = $this->cleanUrl($identifier, $page);

        return $clearUrl === \Amasty\Blog\Helper\Url::ROUTE_SEARCH
            || stristr($clearUrl, '/', true) == \Amasty\Blog\Helper\Url::ROUTE_SEARCH; // for back compatibility
    }

    /**
     * @param $identifier
     * @param $page
     * @param $route
     * @return bool
     */
    private function getUrlKey($identifier, $page, $route)
    {
        $clean = $this->cleanUrl($identifier, $page);

        if (strpos($clean, "/") === false) {
            return false;
        }

        $parts = explode("/", $clean);

        if ($parts[0] !== $route) {
            return false;
        }

        $urlKey = $parts[1];

        return $urlKey;
    }

    /**
     * @param $identifier
     * @param int $page
     * @return bool|string
     */
    private function cleanUrl($identifier, $page = 1)
    {
        $identifier = stristr($identifier, '?', true) ?: $identifier;
        $clean = substr($identifier, strlen($this->settings->getSeoRoute()), strlen($identifier));
        $clean = trim($clean, "/");
        $clean = str_replace([$this->getUrlPostfix(), $this->getOldUrlPostfix($page)], "", $clean);
        $clean = urldecode($clean);

        return $clean;
    }

    /**
     * @param int $page
     * @return string
     */
    private function getUrlPostfix()
    {
        return $this->settings->getBlogPostfix();
    }

    /**
     * @param int $page
     * @return string
     */
    private function getOldUrlPostfix($page = 1)
    {
        $postfix = $this->settings->getBlogPostfix();

        return $page > 1 ? "/{$page}{$postfix}" : $postfix;
    }

    /**
     * @param $identifier
     * @param $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isRightPostSyntax($identifier, $id)
    {
        $url = $this->urlResolver->getPostUrlById($id);

        return $this->isRightSyntax($identifier, $url);
    }

    /**
     * @param $identifier
     * @param $id
     * @param int $page
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isRightCategorySyntax($identifier, $id, $page = 1)
    {
        $url = $this->urlResolver->getCategoryUrlById($id, $page);

        return $this->isRightSyntax($identifier, $url);
    }

    /**
     * @param $identifier
     * @param $id
     * @param int $page
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isRightTagSyntax($identifier, $id, $page = 1)
    {
        $url = $this->urlResolver->getTagUrlById($id, $page);

        return $this->isRightSyntax($identifier, $url);
    }

    /**
     * @param $identifier
     * @param $id
     * @param int $page
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isRightAuthorSyntax($identifier, $id, $page = 1)
    {
        $url = $this->urlResolver->getAuthorUrlById($id, $page);

        return $this->isRightSyntax($identifier, $url);
    }

    /**
     * @param $identifier
     * @param int $page
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isRightBlogSyntax($identifier, $page = 1)
    {
        $url = $this->urlResolver->getBlogUrl($page);

        return $this->isRightSyntax($identifier, $url);
    }

    /**
     * @param $identifier
     * @param $url
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function isRightSyntax($identifier, $url)
    {
        if (!$this->settings->getRedirectToSeoFormattedUrl()) {
            return true;
        }

        $stdPage = (bool)$this->request->getParam($this->pager->getPageVarName());
        $required = str_replace($this->storeManager->getStore()->getBaseUrl(), '', $url);

        return (strtolower($identifier) == strtolower($required)) && !$stdPage;
    }
}
