<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Api\Data;

/**
 * Interface VoteInterface
 */
interface VoteInterface
{
    const MAIN_TABLE = 'amasty_blog_post_helpful';

    const VOTE_ID = 'vote_id';
    const POST_ID = 'post_id';
    const TYPE = 'type';
    const IP = 'ip';

    /**
     * Returns vote id field
     *
     * @return int|null
     */
    public function getVoteId();

    /**
     * @param int $voteId
     *
     * @return $this
     */
    public function setVoteId($voteId);

    /**
     * Returns post id field
     *
     * @return int|null
     */
    public function getPostId();

    /**
     * @param int $reviewId
     *
     * @return $this
     */
    public function setPostId($reviewId);

    /**
     * Returns vote path
     *
     * @return int|null
     */
    public function getType();

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type);

    /**
     * Returns vote path
     *
     * @return string|null
     */
    public function getIp();

    /**
     * @param string $ip
     *
     * @return $this
     */
    public function setIp($ip);
}
