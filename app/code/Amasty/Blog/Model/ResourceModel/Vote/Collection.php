<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\ResourceModel\Vote;

use Amasty\Blog\Model\Vote;
use Amasty\Blog\Model\ResourceModel\Vote as ResourceVote;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 */
class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(
            Vote::class,
            ResourceVote::class
        );
    }
}
