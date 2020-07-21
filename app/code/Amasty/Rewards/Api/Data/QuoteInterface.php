<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Api\Data;

interface QuoteInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const ID = 'id';
    const QUOTE_ID = 'quote_id';
    const REWARD_POINTS = 'reward_points';
    /**#@-*/

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return \Amasty\Rewards\Api\Data\QuoteInterface
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getQuoteId();

    /**
     * @param int $quoteId
     *
     * @return \Amasty\Rewards\Api\Data\QuoteInterface
     */
    public function setQuoteId($quoteId);

    /**
     * @return float
     */
    public function getRewardPoints();

    /**
     * @param float $rewardPoints
     *
     * @return \Amasty\Rewards\Api\Data\QuoteInterface
     */
    public function setRewardPoints($rewardPoints);
}
