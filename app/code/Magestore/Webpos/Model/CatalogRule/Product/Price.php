<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\CatalogRule\Product;

use \Magestore\Webpos\Api\Data\CatalogRule\Product\PriceInterface;
/**
 * Class Price
 * @package Magestore\Webpos\Model\CatalogRule\Product
 */
class Price extends \Magento\Framework\Model\AbstractModel implements PriceInterface
{
    /**
     * Get rule product price id
     *
     * @return int|null
     */
    public function getRuleProductPriceId() {
        return $this->getData(self::RULE_PRODUCT_PRICE_ID);
    }

    /**
     * Get rule date
     *
     * @return string|null
     */
    public function getRuleDate(){
        return $this->getData(self::RULE_DATE);
    }

    /**
     * Get customer group id
     *
     * @return int|null
     */
    public function getCustomerGroupId(){
        return $this->getData(self::CUSTOMER_GROUP_ID);
    }

    /**
     * Get product id
     *
     * @return int|null
     */
    public function getProductId(){
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * Get rule price
     *
     * @return float|null
     */
    public function getRulePrice(){
        return $this->getData(self::RULE_PRICE);
    }

    /**
     * Get website id
     *
     * @return int|null
     */
    public function getWebsiteId(){
        return $this->getData(self::WEBSITE_ID);
    }

    /**
     * Get latest start date
     *
     * @return string|null
     */
    public function getLatestStartDate(){
        return $this->getData(self::LATEST_START_DATE);
    }

    /**
     * Get earliest end date
     *
     * @return string|null
     */
    public function getEarliestEndDate(){
        return $this->getData(self::EARLIEST_END_DATE);
    }

    /**
     * Get udpated time
     *
     * @return string|null
     */
    public function getUpdatedTime(){
        return $this->getData(self::UPDATED_TIME);
    }

    /**
     * Set rule product price id
     *
     * @param int $ruleProductPriceId
     * @return PriceInterface
     */
    public function setRuleProductPriceId($ruleProductPriceId){
        return $this->setData(self::RULE_PRODUCT_PRICE_ID, $ruleProductPriceId);
    }

    /**
     * Set rule date
     *
     * @param string|null $ruleDate
     * @return PriceInterface
     */
    public function setRuleDate($ruleDate){
        return $this->setData(self::RULE_DATE, $ruleDate);
    }

    /**
     * Set customer group id
     *
     * @param int|null $customerGroupId
     * @return PriceInterface
     */
    public function setCustomerGroupId($customerGroupId){
        return $this->setData(self::CUSTOMER_GROUP_ID, $customerGroupId);
    }

    /**
     * Set product id
     *
     * @param int|null $productId
     * @return PriceInterface
     */
    public function setProductId($productId){
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * Set rule price
     *
     * @param float|null $rulePrice
     * @return PriceInterface
     */
    public function setRulePrice($rulePrice){
        return $this->setData(self::RULE_PRICE, $rulePrice);
    }

    /**
     * Set website id
     *
     * @param int|null $websiteId
     * @return PriceInterface
     */
    public function setWebsiteId($websiteId){
        return $this->setData(self::WEBSITE_ID, $websiteId);
    }

    /**
     * Set latest start date
     *
     * @param string|null $latestStartDate
     * @return PriceInterface
     */
    public function setLatestStartDate($latestStartDate){
        return $this->setData(self::LATEST_START_DATE, $latestStartDate);
    }

    /**
     * Set earliest end date
     *
     * @param string|null $earliestEndDate
     * @return PriceInterface
     */
    public function setEarliestEndDate($earliestEndDate){
        return $this->setData(self::EARLIEST_END_DATE, $earliestEndDate);
    }

    /**
     * Set udpated time
     *
     * @param string|null $updatedTime
     * @return PriceInterface
     */
    public function setUpdatedTime($updatedTime){
        return $this->setData(self::UPDATED_TIME, $updatedTime);
    }
    /**
     * @inheritDoc
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\CatalogRule\Product\PriceExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
