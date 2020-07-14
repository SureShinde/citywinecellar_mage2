<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\ResourceModel\Author;

use Amasty\Blog\Api\Data\AuthorInterface;
use Amasty\Blog\Model\ResourceModel\Traits\CollectionTrait;
use Amasty\Blog\Model\ResourceModel\Traits\StoreCollectionTrait;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    use StoreCollectionTrait;
    use CollectionTrait;

    const MULTI_STORE_FIELDS_MAP = [
        AuthorInterface::NAME => 'IFNULL(noDefaultStore.name, store.name)',
        AuthorInterface::META_TITLE => 'IFNULL(noDefaultStore.meta_title, store.meta_title)',
        AuthorInterface::META_DESCRIPTION => 'IFNULL(noDefaultStore.meta_description, store.meta_description)',
        AuthorInterface::META_TAGS => 'IFNULL(noDefaultStore.meta_tags, store.meta_tags)',
    ];

    /**
     * @var string
     */
    protected $_idFieldName = 'author_id';

    /**
     * @var array
     */
    protected $_map = [
        'fields' => [
            'author_id' => 'main_table.author_id'
        ]
    ];

    /**
     * @var string
     */
    private $queryText;

    public function _construct()
    {
        $this->_init(\Amasty\Blog\Model\Author::class, \Amasty\Blog\Model\ResourceModel\Author::class);
    }

    /**
     * @return string
     */
    public function getQueryText()
    {
        return $this->queryText;
    }

    /**
     * @param string $queryText
     */
    public function setQueryText(string $queryText)
    {
        $this->queryText = $queryText;
    }
}
