<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Api\Data;

interface RewardsInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const ID = 'id';
    const ACTION_DATE = 'action_date';
    const AMOUNT = 'amount';
    const COMMENT = 'comment';
    const ACTION = 'action';
    const POINTS_LEFT = 'points_left';
    const CUSTOMER_ID = 'customer_id';
    const EXPIRATION_ID = 'expiration_id';
    /**#@-*/

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return \Amasty\Rewards\Api\Data\RewardsInterface
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getActionDate();

    /**
     * @param string $actionDate
     *
     * @return \Amasty\Rewards\Api\Data\RewardsInterface
     */
    public function setActionDate($actionDate);

    /**
     * @return float
     */
    public function getAmount();

    /**
     * @param float $amount
     *
     * @return \Amasty\Rewards\Api\Data\RewardsInterface
     */
    public function setAmount($amount);

    /**
     * @return string
     */
    public function getComment();

    /**
     * @param string $comment
     *
     * @return \Amasty\Rewards\Api\Data\RewardsInterface
     */
    public function setComment($comment);

    /**
     * @return string
     */
    public function getAction();

    /**
     * @param string $action
     *
     * @return \Amasty\Rewards\Api\Data\RewardsInterface
     */
    public function setAction($action);

    /**
     * @return float
     */
    public function getPointsLeft();

    /**
     * @param float $pointsLeft
     *
     * @return \Amasty\Rewards\Api\Data\RewardsInterface
     */
    public function setPointsLeft($pointsLeft);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $customerId
     *
     * @return \Amasty\Rewards\Api\Data\RewardsInterface
     */
    public function setCustomerId($customerId);

    /**
     * @return int
     */
    public function getExpirationId();

    /**
     * @param int $expirationId
     *
     * @return \Amasty\Rewards\Api\Data\RewardsInterface
     */
    public function setExpirationId($expirationId);
}
