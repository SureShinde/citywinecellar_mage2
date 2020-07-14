<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model;

use Amasty\Blog\Api\Data\PostInterface;
use Amasty\Blog\Helper\Image;
use Magento\Framework\DataObject\IdentityInterface;
use Amasty\Blog\Model\ResourceModel\Posts as PostResource;
use Amasty\Blog\Model\Source\PostStatus;

class Posts extends AbstractModel implements IdentityInterface, PostInterface
{
    const CACHE_TAG = 'amblog_post';

    const PERSISTENT_NAME = 'amasty_blog_posts';

    const CUT_LIMITER = '<!-- blogcut -->';

    const CUT_LIMITER_TAG = '<hr class="cutter">';

    const PATTERN_LIMITER_FIND = '/<hr\s+class\s*=\s*\".*?\bcutter\b.*?\".*?>/i';

    const PATTERN_LIMIRER_CUT_AFTER = '/<hr\s+class\s*=\s*\".*?\bcutter\b.*?\".*?>.*/ism';

    /**
     * @var bool
     */
    private $isPreviewPost = false;

    /**
     * @var \Amasty\Blog\Helper\Url
     */
    private $urlHelper;

    /**
     * @var Image
     */
    private $imageHelper;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    private $filterProvider;

    /**
     * @var \Amasty\Blog\Helper\Settings
     */
    private $settingHelper;

    /**
     * @var \Amasty\Blog\Api\ViewRepositoryInterface
     */
    private $viewRepository;

    /**
     * @var \Amasty\Blog\Api\AuthorRepositoryInterface
     */
    private $authorRepository;

    public function _construct()
    {
        parent::_construct();
        $this->_cacheTag = self::CACHE_TAG;
        $this->urlHelper = $this->getData('url_helper');
        $this->imageHelper = $this->getData('image_helper');
        $this->settingHelper = $this->getData('setting_helper');
        $this->filterProvider = $this->getData('filter_provider');
        $this->viewRepository = $this->getData('view_repository');
        $this->authorRepository = $this->getData('author_repository');
        $this->_init(PostResource::class);
    }

    /**
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function beforeSave()
    {
        if (!$this->urlHelper->validate($this->getUrlKey())) {
            $this->setUrlKey($this->urlHelper->prepare($this->getUrlKey()));
        }

        return parent::beforeSave();
    }

    /**
     * @return string
     */
    public function getTags()
    {
        return $this->_getData(PostInterface::TAGS);
    }

    /**
     * @return array
     */
    public function getTagIds()
    {
        return $this->_getData(PostInterface::TAG_IDS);
    }

    /**
     * @return array
     */
    public function getStores()
    {
        return $this->_getData(PostInterface::STORES);
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        return $this->_getData(PostInterface::CATEGORIES);
    }

    /**
     * @return bool
     */
    public function isFeatured()
    {
        return $this->_getData(PostInterface::IS_FEATURED);
    }

    /**
     * @return int
     */
    public function getViews()
    {
        return $this->getData(self::VIEWS) + $this->getFlyViews();
    }

    /**
     * @return int
     */
    private function getFlyViews()
    {
        return $this->viewRepository->getViewCountByPostId($this->getPostId());
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getFullContent()
    {
        return $this->getContent(PostInterface::FULL_CONTENT);
    }

    /**
     * @param $key
     * @return string
     * @throws \Exception
     */
    private function getContent($key)
    {
        $content = $this->getData($key) ? : '';
        $content = $this->filterProvider->getPageFilter()->filter($content);

        return $content;
    }

    /**
     * @return string|null
     * @throws \Exception
     */
    public function getShortContent()
    {
        if ($this->getDisplayShortContent()) {
            $content = $this->getContent(PostInterface::SHORT_CONTENT);
        } else {
            $content = str_replace(self::CUT_LIMITER, self::CUT_LIMITER_TAG, $this->getFullContent());
            preg_match_all(self::PATTERN_LIMITER_FIND, $content, $matches);
            if (isset($matches[0][0])) {
                $content = preg_replace(self::PATTERN_LIMIRER_CUT_AFTER, '', $content);
            }
        }

        return str_replace('target="_self"', '', $content);
    }

    /**
     * @return bool
     */
    public function hasThumbnail()
    {
        return $this->getPostThumbnail() || $this->getListThumbnail();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPostThumbnailSrc()
    {
        $src = $this->getPostThumbnail() ?: $this->getListThumbnail();

        return $src ? $this->getResizedImage($src) : $this->imageHelper->getImageUrl($src);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPostSidebarSrc()
    {
        $src = $this->getPostThumbnail() ?: $this->getListThumbnail();
        if ($src) {
            $result = $this->imageHelper->getResizedImageUrl(
                $src,
                $this->settingHelper->getRecentPostsImageWidth(),
                $this->settingHelper->getRecentPostsImageHeight()
            );
        } else {
            $result = $this->imageHelper->getImageUrl($src);
        }

        return $result;
    }

    /**
     * @param $src
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getResizedImage($src)
    {
        $imageWidth = $this->settingHelper->getImageWidth();
        $imageHeight = $this->settingHelper->getImageHeight();

        return $this->imageHelper->getResizedImageUrl($src, $imageWidth, $imageHeight);
    }

    /**
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPostImageSrc()
    {
        $src = $this->getPostThumbnail() ?: $this->getListThumbnail();

        return $src ? $this->imageHelper->getImageUrl($src) : $src;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getListThumbnailSrc()
    {
        $src = $this->getListThumbnail() ?: $this->getPostThumbnail();

        return $src ? $this->getResizedImage($src) : '';
    }

    /**
     * @return bool
     */
    public function isScheduled()
    {
        return $this->getStatus() == PostStatus::STATUS_SCHEDULED;
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
     * Return value of the alt HTML attribute for thumbnail
     *
     * @param string $type
     *
     * @return string
     */
    public function getThumbnailAlt($type = 'list')
    {
        return $this->getData($type . '_thumbnail_alt') ? : $this->getTitle();
    }

    /**
     * @return int
     */
    public function getPostId()
    {
        return $this->_getData(PostInterface::POST_ID);
    }

    /**
     * @param int $postId
     * @return $this|PostInterface
     */
    public function setPostId($postId)
    {
        $this->setData(PostInterface::POST_ID, $postId);

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->_getData(PostInterface::STATUS);
    }

    /**
     * @param int $status
     * @return $this|PostInterface
     */
    public function setStatus($status)
    {
        $this->setData(PostInterface::STATUS, $status);

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->_getData(PostInterface::TITLE);
    }

    /**
     * @param string $title
     * @return $this|PostInterface
     */
    public function setTitle($title)
    {
        $this->setData(PostInterface::TITLE, $title);

        return $this;
    }

    /**
     * @return string
     */
    public function getRelatedPostIds()
    {
        return $this->_getData(PostInterface::RELATED_POST_IDS);
    }

    /**
     * @param string $ids
     * @return $this|PostInterface
     */
    public function setRelatedPostIds($ids)
    {
        $this->setData(PostInterface::RELATED_POST_IDS, $ids);

        return $this;
    }

    /**
     * @return string
     */
    public function getUrlKey()
    {
        return $this->_getData(PostInterface::URL_KEY);
    }

    /**
     * @param string $urlKey
     * @return $this|PostInterface
     */
    public function setUrlKey($urlKey)
    {
        $this->setData(PostInterface::URL_KEY, $urlKey);

        return $this;
    }

    /**
     * @param string|null $shortContent
     * @return $this|PostInterface
     */
    public function setShortContent($shortContent)
    {
        $this->setData(PostInterface::SHORT_CONTENT, $shortContent);

        return $this;
    }

    /**
     * @param string $fullContent
     * @return $this|PostInterface
     */
    public function setFullContent($fullContent)
    {
        $this->setData(PostInterface::FULL_CONTENT, $fullContent);

        return $this;
    }

    /**
     * @return string
     */
    public function getPostedBy()
    {
        return $this->getAuthor()->getName();
    }

    /**
     * @param string $postedBy
     * @return $this|PostInterface
     */
    public function setPostedBy($postedBy)
    {
        $this->getAuthor()->setName($postedBy);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFacebookProfile()
    {
        return $this->getAuthor()->getFacebookProfile();
    }

    /**
     * @param string|null $facebookProfile
     * @return $this|PostInterface
     */
    public function setFacebookProfile($facebookProfile)
    {
        $this->getAuthor()->setFacebookProfile($facebookProfile);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTwitterProfile()
    {
        return $this->getAuthor()->getTwitterProfile();
    }

    /**
     * @param string|null $twitterProfile
     * @return $this|PostInterface
     */
    public function setTwitterProfile($twitterProfile)
    {
        $this->setTwitterProfile($twitterProfile);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMetaTitle()
    {
        return $this->_getData(PostInterface::META_TITLE);
    }

    /**
     * @param string|null $metaTitle
     * @return $this|PostInterface
     */
    public function setMetaTitle($metaTitle)
    {
        $this->setData(PostInterface::META_TITLE, $metaTitle);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMetaTags()
    {
        return $this->_getData(PostInterface::META_TAGS);
    }

    /**
     * @param string|null $metaTags
     * @return $this|PostInterface
     */
    public function setMetaTags($metaTags)
    {
        $this->setData(PostInterface::META_TAGS, $metaTags);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMetaDescription()
    {
        return $this->_getData(PostInterface::META_DESCRIPTION);
    }

    /**
     * @param string|null $metaDescription
     * @return $this|PostInterface
     */
    public function setMetaDescription($metaDescription)
    {
        $this->setData(PostInterface::META_DESCRIPTION, $metaDescription);

        return $this;
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->_getData(PostInterface::CREATED_AT);
    }

    /**
     * @param string $createdAt
     * @return $this|PostInterface
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(PostInterface::CREATED_AT, $createdAt);

        return $this;
    }

    /**
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->_getData(PostInterface::UPDATED_AT);
    }

    /**
     * @param string $updatedAt
     * @return $this|PostInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->setData(PostInterface::UPDATED_AT, $updatedAt);

        return $this;
    }

    /**
     * @return string
     */
    public function getPublishedAt()
    {
        return $this->_getData(PostInterface::PUBLISHED_AT);
    }

    /**
     * @param string $publishedAt
     * @return $this|PostInterface
     */
    public function setPublishedAt($publishedAt)
    {
        $this->setData(PostInterface::PUBLISHED_AT, $publishedAt);

        return $this;
    }

    /**
     * @return string
     */
    public function getRecentlyCommentedAt()
    {
        return $this->_getData(PostInterface::RECENTLY_COMMENTED_AT);
    }

    /**
     * @param string $recentlyCommentedAt
     * @return $this|PostInterface
     */
    public function setRecentlyCommentedAt($recentlyCommentedAt)
    {
        $this->setData(PostInterface::RECENTLY_COMMENTED_AT, $recentlyCommentedAt);

        return $this;
    }

    /**
     * @return int
     */
    public function getUserDefinePublish()
    {
        return $this->_getData(PostInterface::USER_DEFINE_PUBLISH);
    }

    /**
     * @param int $userDefinePublish
     * @return $this|PostInterface
     */
    public function setUserDefinePublish($userDefinePublish)
    {
        $this->setData(PostInterface::USER_DEFINE_PUBLISH, $userDefinePublish);

        return $this;
    }

    /**
     * @return int
     */
    public function getNotifyOnEnable()
    {
        return $this->_getData(PostInterface::NOTIFY_ON_ENABLE);
    }

    /**
     * @param int $notifyOnEnable
     * @return $this|PostInterface
     */
    public function setNotifyOnEnable($notifyOnEnable)
    {
        $this->setData(PostInterface::NOTIFY_ON_ENABLE, $notifyOnEnable);

        return $this;
    }

    /**
     * @return int
     */
    public function getDisplayShortContent()
    {
        return $this->_getData(PostInterface::DISPLAY_SHORT_CONTENT);
    }

    /**
     * @param int $displayShortContent
     * @return $this|PostInterface
     */
    public function setDisplayShortContent($displayShortContent)
    {
        $this->setData(PostInterface::DISPLAY_SHORT_CONTENT, $displayShortContent);

        return $this;
    }

    /**
     * @return int
     */
    public function getCommentsEnabled()
    {
        return $this->_getData(PostInterface::COMMENTS_ENABLED);
    }

    /**
     * @param int $commentsEnabled
     * @return $this|PostInterface
     */
    public function setCommentsEnabled($commentsEnabled)
    {
        $this->setData(PostInterface::COMMENTS_ENABLED, $commentsEnabled);

        return $this;
    }

    /**
     * @param int $views
     * @return $this|PostInterface
     */
    public function setViews($views)
    {
        $this->setData(PostInterface::VIEWS, $views);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPostThumbnail()
    {
        return $this->_getData(PostInterface::POST_THUMBNAIL);
    }

    /**
     * @param string|null $postThumbnail
     * @return $this|PostInterface
     */
    public function setPostThumbnail($postThumbnail)
    {
        $this->setData(PostInterface::POST_THUMBNAIL, $postThumbnail);

        return $this;
    }

    /**
     * @param $name
     * @param $thumbnail
     *
     * @return $this
     */
    public function setThumbnail($name, $thumbnail)
    {
        return $this->setData($name, $thumbnail);
    }

    /**
     * @return string|null
     */
    public function getListThumbnail()
    {
        return $this->_getData(PostInterface::LIST_THUMBNAIL);
    }

    /**
     * @param string|null $listThumbnail
     * @return $this|PostInterface
     */
    public function setListThumbnail($listThumbnail)
    {
        $this->setData(PostInterface::LIST_THUMBNAIL, $listThumbnail);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getThumbnailUrl()
    {
        return $this->_getData(PostInterface::THUMBNAIL_URL);
    }

    /**
     * @param string|null $thumbnailUrl
     * @return $this|PostInterface
     */
    public function setThumbnailUrl($thumbnailUrl)
    {
        $this->setData(PostInterface::THUMBNAIL_URL, $thumbnailUrl);

        return $this;
    }

    /**
     * @return string
     */
    public function getGridClass()
    {
        return $this->_getData(PostInterface::GRID_CLASS);
    }

    /**
     * @param string $gridClass
     * @return $this|PostInterface
     */
    public function setGridClass($gridClass)
    {
        $this->setData(PostInterface::GRID_CLASS, $gridClass);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCanonicalUrl()
    {
        return $this->_getData(PostInterface::CANONICAL_URL);
    }

    /**
     * @param string|null $canonicalUrl
     * @return $this|PostInterface
     */
    public function setCanonicalUrl($canonicalUrl)
    {
        $this->setData(PostInterface::CANONICAL_URL, $canonicalUrl);

        return $this;
    }

    /**
     * @return string
     */
    public function getPostThumbnailAlt()
    {
        return $this->_getData(PostInterface::POST_THUMBNAIL_ALT);
    }

    /**
     * @param string $postThumbnailAlt
     * @return $this|PostInterface
     */
    public function setPostThumbnailAlt($postThumbnailAlt)
    {
        $this->setData(PostInterface::POST_THUMBNAIL_ALT, $postThumbnailAlt);

        return $this;
    }

    /**
     * @return string
     */
    public function getListThumbnailAlt()
    {
        return $this->_getData(PostInterface::LIST_THUMBNAIL_ALT);
    }

    /**
     * @param string $listThumbnailAlt
     * @return $this|PostInterface
     */
    public function setListThumbnailAlt($listThumbnailAlt)
    {
        $this->setData(PostInterface::LIST_THUMBNAIL_ALT, $listThumbnailAlt);

        return $this;
    }

    /**
     * @return string
     */
    public function getAmpPostContent()
    {
        $content = preg_replace(
            '/<img(.+?)\/?>/is',
            '<div class="amp-img-container"><amp-img $1 layout="fill"></amp-img></div>',
            $this->getFullContent()
        );

        $content = preg_replace(
            '/<script.+?\/script>/is',
            '',
            $content
        );

        $content = preg_replace(
            '/<video(.+?)\>(.+?)<\/video>/is',
            '<amp-video $1 layout="responsive">$2</amp-video>',
            $content
        );

        return $content;
    }

    /**
     * @return \Amasty\Blog\Api\Data\AuthorInterface
     */
    public function getAuthor()
    {
        if (!$this->getData(PostInterface::AUTHOR)) {
            $author = $this->authorRepository->getByIdAndStore(
                $this->getData(PostInterface::AUTHOR_ID),
                $this->getCurrentStoreId()
            );
            $this->setData(PostInterface::AUTHOR, $author);
        }

        return $this->getData(PostInterface::AUTHOR);
    }

    /**
     * @return int
     */
    public function getAuthorId()
    {
        return $this->getData(PostInterface::AUTHOR_ID);
    }

    /**
     * @param int $authorId
     * @return $this
     */
    public function setAuthorId($authorId)
    {
        $this->setData(PostInterface::AUTHOR_ID, $authorId);

        return $this;
    }

    /**
     * @param \Amasty\Blog\Api\Data\AuthorInterface $author
     * @return $this
     */
    public function setAuthor(\Amasty\Blog\Api\Data\AuthorInterface $author)
    {
        $this->setData(PostInterface::AUTHOR, $author);

        return $this;
    }

    /**
     * @return bool
     */
    public function isPreviewPost()
    {
        return $this->isPreviewPost;
    }

    /**
     * @param bool $isPreviewPost
     */
    public function setIsPreviewPost($isPreviewPost)
    {
        $this->isPreviewPost = $isPreviewPost;
    }
}
