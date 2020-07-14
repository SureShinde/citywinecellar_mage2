<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\CatalogRule\Product;

/**
 * Interface PriceInterface
 * @package Magestore\Webpos\Api\Data\CatalogRule\Product
 */
interface PriceInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const RULE_PRODUCT_PRICE_ID = 'rule_product_price_id';
    const RULE_DATE = 'rule_date';
    const CUSTOMER_GROUP_ID = 'customer_group_id';
    const PRODUCT_ID = 'product_id';
    const RULE_PRICE = 'rule_price';
    const WEBSITE_ID = 'website_id';
    const LATEST_START_DATE = 'latest_start_date';
    const EARLIEST_END_DATE = 'earliest_end_date';
    const UPDATED_TIME = 'updated_time';

    /**
     * Get rule product price id
     *
     * @return int|null
     */
    public function getRuleProductPriceId();

    /**
     * Get rule date
     *
     * @return string|null
     */
    public function getRuleDate();

    /**
     * Get customer group id
     *
     * @return int|null
     */
    public function getCustomerGroupId();

    /**
     * Get product id
     *
     * @return int|null
     */
    public function getProductId();

    /**
     * Get rule price
     *
     * @return float|null
     */
    public function getRulePrice();

    /**
     * Get website id
     *
     * @return int|null
     */
    public function getWebsiteId();

    /**
     * Get latest start date
     *
     * @return string|null
     */
    public function getLatestStartDate();

    /**
     * Get earliest end date
     *
     * @return string|null
     */
    public function getEarliestEndDate();

    /**
     * Get udpated time
     *
     * @return string|null
     */
    public function getUpdatedTime();

    /**
     * Set rule product price id
     *
     * @param int $ruleProductPriceId
     * @return PriceInterface
     */
    public function setRuleProductPriceId($ruleProductPriceId);

    /**
     * Set rule date
     *
     * @param string|null $ruleDate
     * @return PriceInterface
     */
    public function setRuleDate($ruleDate);

    /**
     * Set customer group id
     *
     * @param int|null $customerGroupId
     * @return PriceInterface
     */
    public function setCustomerGroupId($customerGroupId);

    /**
     * Set product id
     *
     * @param int|null $productId
     * @return PriceInterface
     */
    public function setProductId($productId);

    /**
     * Set rule price
     *
     * @param float|null $rulePrice
     * @return PriceInterface
     */
    public function setRulePrice($rulePrice);

    /**
     * Set website id
     *
     * @param int|null $websiteId
     * @return PriceInterface
     */
    public function setWebsiteId($websiteId);

    /**
     * Set latest start date
     *
     * @param string|null $latestStartDate
     * @return PriceInterface
     */
    public function setLatestStartDate($latestStartDate);

    /**
     * Set earliest end date
     *
     * @param string|null $earliestEndDate
     * @return PriceInterface
     */
    public function setEarliestEndDate($earliestEndDate);

    /**
     * Set udpated time
     *
     * @param string|null $updatedTime
     * @return PriceInterface
     */
    public function setUpdatedTime($updatedTime);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\CatalogRule\Product\PriceExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\CatalogRule\Product\PriceExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\CatalogRule\Product\PriceExtensionInterface $extensionAttributes
    );
}
