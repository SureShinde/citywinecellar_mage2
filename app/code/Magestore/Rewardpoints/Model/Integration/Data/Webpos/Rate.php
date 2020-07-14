<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Rewardpoints\Model\Integration\Data\Webpos;

/**
 * Class Rate
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Rate extends \Magento\Framework\DataObject implements \Magestore\Rewardpoints\Api\Data\Integration\Webpos\RateInterface
{

    /**
     * @param $rateId
     * @return $this|void
     */
    public function setRateId($rateId)
    {
        $this->setData(self::RATE_ID, $rateId);
    }

    /**
     *
     * @return int|null
     */
    public function getRateId() {
        return $this->getData(self::RATE_ID);
    }

    /**
     * @param $websiteIds
     * @return $this|void
     */
    public function setWebsiteIds($websiteIds)
    {
        $this->setData(self::WEBSITE_IDS, $websiteIds);
    }

    /**
     *
     * @return string|null
     */
    public function getWebsiteIds() {
        return $this->getData(self::WEBSITE_IDS);
    }

    /**
     * @param $customerGroupIds
     * @return $this|void
     */
    public function setCustomerGroupIds($customerGroupIds)
    {
        $this->setData(self::CUSTOMER_GROUP_IDS, $customerGroupIds);
    }

    /**
     *
     * @return string|null
     */
    public function getCustomerGroupIds() {
        return $this->getData(self::CUSTOMER_GROUP_IDS);
    }

    /**
     * @param $direction
     * @return $this|void
     */
    public function setDirection($direction)
    {
        $this->setData(self::DIRECTION, $direction);
    }

    /**
     *
     * @return int|null
     */
    public function getDirection() {
        return $this->getData(self::DIRECTION);
    }

    /**
     * @param $points
     * @return $this|void
     */
    public function setPoints($points)
    {
        $this->setData(self::POINTS, $points);
    }

    /**
     * @return int|null
     */
    public function getPoints() {
        return $this->getData(self::POINTS);
    }

    /**
     * @param $money
     * @return $this|void
     */
    public function setMoney($money)
    {
        $this->setData(self::MONEY, $money);
    }

    /**
     * @return float|null
     */
    public function getMoney() {
        return $this->getData(self::MONEY);
    }

    /**
     * @param $maxPriceSpendedType
     * @return $this|void
     */
    public function setMaxPriceSpendedType($maxPriceSpendedType)
    {
        $this->setData(self::MAX_PRICE_SPENDED_TYPE, $maxPriceSpendedType);
    }

    /**
     * @return string|null
     */
    public function getMaxPriceSpendedType() {
        return $this->getData(self::MAX_PRICE_SPENDED_TYPE);
    }

    /**
     * @param $maxPriceSpendedTValue
     * @return $this|void
     */
    public function setMaxPriceSpendedValue($maxPriceSpendedTValue)
    {
        $this->setData(self::MAX_PRICE_SPENDED_VALUE, $maxPriceSpendedTValue);
    }

    /**
     * @return float|null
     */
    public function getMaxPriceSpendedValue() {
        return $this->getData(self::MAX_PRICE_SPENDED_VALUE);
    }

    /**
     * @param $status
     * @return $this|void
     */
    public function setStatus($status)
    {
        $this->setData(self::STATUS, $status);
    }

    /**
     * @return int|null
     */
    public function getStatus() {
        return $this->getData(self::STATUS);
    }

    /**
     * @param $sortOrder
     * @return $this|void
     */
    public function setSortOrder($sortOrder)
    {
        $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * @return int|null
     */
    public function getSortOrder() {
        return $this->getData(self::SORT_ORDER);
    }

}
