<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model;

use Amasty\Blog\Api\Data\VoteInterface;

/**
 * Class Vote
 */
class Vote extends \Magento\Framework\Model\AbstractModel implements VoteInterface
{
    public function _construct()
    {
        $this->_init(\Amasty\Blog\Model\ResourceModel\Vote::class);
    }

    /**
     * Returns vote id field
     *
     * @return int|null
     */
    public function getVoteId()
    {
        return $this->getData(self::VOTE_ID);
    }

    /**
     * @param int $voteId
     *
     * @return $this
     */
    public function setVoteId($voteId)
    {
        $this->setData(self::VOTE_ID, $voteId);
        return $this;
    }

    /**
     * Returns post id field
     *
     * @return int|null
     */
    public function getPostId()
    {
        return $this->getData(self::POST_ID);
    }

    /**
     * @param int $postId
     *
     * @return $this
     */
    public function setPostId($postId)
    {
        $this->setData(self::POST_ID, $postId);
        return $this;
    }

    /**
     * Returns vote type
     *
     * @return int|null
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @param int $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->setData(self::TYPE, $type);
        return $this;
    }

    /**
     * Returns vote type
     *
     * @return string|null
     */
    public function getIp()
    {
        return $this->getData(self::IP);
    }

    /**
     * @param string $ip
     *
     * @return $this
     */
    public function setIp($ip)
    {
        $this->setData(self::IP, $ip);
        return $this;
    }
}
