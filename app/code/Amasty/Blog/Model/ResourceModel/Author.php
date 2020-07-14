<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\ResourceModel;

use Amasty\Blog\Api\Data\AuthorInterface;
use Amasty\Blog\Model\ResourceModel\Traits\ResourceModelTrait;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Author extends AbstractDb
{
    use ResourceModelTrait;

    const TABLE_NAME = 'amasty_blog_author';

    const STORE_TABLE_NAME = 'amasty_blog_author_store';

    const STORE_TABLE_FIELDS = [
        AuthorInterface::AUTHOR_ID,
        AuthorInterface::STORE_ID,
        AuthorInterface::NAME,
        AuthorInterface::META_TITLE,
        AuthorInterface::META_DESCRIPTION,
        AuthorInterface::META_TAGS,
    ];

    /**
     * @var \Amasty\Blog\Model\AuthorFactory
     */
    private $authorFactory;

    /**
     * @var Author\CollectionFactory
     */
    private $authorCollectionFactory;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Amasty\Blog\Model\AuthorFactory $authorFactory,
        \Amasty\Blog\Model\ResourceModel\Author\CollectionFactory $authorCollectionFactory,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->authorFactory = $authorFactory;
        $this->authorCollectionFactory = $authorCollectionFactory;
    }

    public function _construct()
    {
        $this->_init(self::TABLE_NAME, AuthorInterface::AUTHOR_ID);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->saveStoreData($object);

        return $this;
    }

    /**
     * @param AbstractModel $object
     * @return $this|AbstractDb
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $this->addDefaultStoreSelect($object);

        return $this;
    }

    /**
     * @param $name
     * @param null $facebookProfile
     * @param null $twitterProfile
     * @return \Amasty\Blog\Api\Data\AuthorInterface
     */
    public function createAuthor($name, $facebookProfile = null, $twitterProfile = null)
    {
        $author = $this->authorFactory->create();
        $author->setName($name)
            ->setFacebookProfile($facebookProfile)
            ->setTwitterProfile($twitterProfile);
        $author->prepapreUrlKey();
        $this->save($author);
        return $author;
    }

    /**
     * @param string $urlKey
     * @return string
     */
    public function getUniqUrlKey($urlKey)
    {
        $collection = $this->authorCollectionFactory->create();
        $collection->getSelect()->where('url_key like "?%"', $urlKey);
        $collection->getSelect()->order('url_key');
        if ($collection->count()) {
            foreach ($collection as $item) {
                if ($item->getUrlKey() == $urlKey) {
                    $urlKey = preg_match('/(.*)-(\d+)$/', $urlKey, $matches)
                        ? $matches[1] . '-' . ($matches[2] + 1)
                        : $urlKey . '-1';
                }
            }
        }
        return $urlKey;
    }
}
