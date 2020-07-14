<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Rewardpoints\Api\Data\Integration\Webpos;

/**
 * Customer interface.
 */
interface RateInterface
{
    const RATE_ID = 'rate_id';
    const WEBSITE_IDS = 'website_ids';
    const CUSTOMER_GROUP_IDS = 'customer_group_ids';
    const DIRECTION = 'direction';
    const POINTS = 'points';
    const MONEY = 'money';
    const MAX_PRICE_SPENDED_TYPE = 'max_price_spended_type';
    const MAX_PRICE_SPENDED_VALUE = 'max_price_spended_value';
    const STATUS = 'status';
    const SORT_ORDER = 'sort_order';

    /**
     * Set rate id
     *
     * @param int|null $rateId
     * @return RateInterface
     */
    public function setRateId($rateId);

    /**
     * Get rate id
     *
     * @return int|null
     */
    public function getRateId();

    /**
     * Set website id
     *
     * @param string|null $websiteIds
     * @return RateInterface
     */
    public function setWebsiteIds($websiteIds);


    /**
     * Get website ids
     *
     * @api
     * @return string|null
     */
    public function getWebsiteIds();

    /**
     * Set customer group ids
     *
     * @param int|null $customerGroupIds
     * @return RateInterface
     */
    public function setCustomerGroupIds($customerGroupIds);


    /**
     * Get customer group ids
     *
     * @api
     * @return string|null
     */
    public function getCustomerGroupIds();

    /**
     * Set direction
     *
     * @param int|null $direction
     * @return RateInterface
     */
    public function setDirection($direction);

    /**
     * Get direction
     *
     * @api
     * @return int|null
     */
    public function getDirection();

    /**
     * Set points
     *
     * @param int|null $points
     * @return RateInterface
     */
    public function setPoints($points);

    /**
     * Get points
     *
     * @api
     * @return int|null
     */
    public function getPoints();


    /**
     * Set money
     *
     * @param float|null $money
     * @return RateInterface
     */
    public function setMoney($money);

    /**
     * Get money
     *
     * @api
     * @return float|null
     */
    public function getMoney();

    /**
     * Set max price spended type
     *
     * @param string|null $maxPriceSpendedType
     * @return RateInterface
     */
    public function setMaxPriceSpendedType($maxPriceSpendedType);

    /**
     * Get max price spended type
     *
     * @api
     * @return string|null
     */
    public function getMaxPriceSpendedType();

    /**
     * Set max price spended value
     *
     * @param float|null $maxPriceSpendedTValue
     * @return RateInterface
     */
    public function setMaxPriceSpendedValue($maxPriceSpendedTValue);

    /**
     * Get max price spended value
     *
     * @api
     * @return float|null
     */
    public function getMaxPriceSpendedValue();

    /**
     * Set status
     *
     * @param int|null $status
     * @return RateInterface
     */
    public function setStatus($status);

    /**
     * Get status
     *
     * @api
     * @return int|null
     */
    public function getStatus();

    /**
     * Set sort order
     *
     * @param int|null $sortOrder
     * @return RateInterface
     */
    public function setSortOrder($sortOrder);

    /**
     * Get sort order
     *
     * @api
     * @return int|null
     */
    public function getSortOrder();

}
