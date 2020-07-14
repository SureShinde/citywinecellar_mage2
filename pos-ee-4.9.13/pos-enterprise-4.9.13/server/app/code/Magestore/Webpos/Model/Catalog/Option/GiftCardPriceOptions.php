<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Catalog\Option;
/**
 * Class GiftCardPriceOptions
 * @package Magestore\Webpos\Model\Catalog\Option
 */
class GiftCardPriceOptions extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsInterface
{
    /**
     * Get gift price type
     *
     * @return int|null
     */
    public function getGiftCardType(){
        return $this->getData(self::GIFT_CARD_TYPE);
    }
    /**
     * Set gift price type
     *
     * @param int $giftCardType
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsInterface
     */
    public function setGiftCardType($giftCardType){
        return $this->setData(self::GIFT_CARD_TYPE, $giftCardType);
    }
    /**
     * Get gift type
     *
     * @return int|null
     */
    public function getGiftType(){
        return $this->getData(self::GIFT_TYPE);
    }
    /**
     * Set gift type
     *
     * @param int $giftType
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsInterface
     */
    public function setGiftType($giftType){
        return $this->setData(self::GIFT_TYPE, $giftType);
    }
    /**
     * Get gift value
     *
     * @return float|null
     */
    public function getGiftValue(){
        return $this->getData(self::GIFT_VALUE);
    }
    /**
     * Set gift value
     *
     * @param float $giftValue
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsInterface
     */
    public function setGiftValue($giftValue){
        return $this->setData(self::GIFT_VALUE, $giftValue);
    }
    /**
     * Get gift from
     *
     * @return float|null
     */
    public function getGiftFrom(){
        return $this->getData(self::GIFT_FROM);
    }
    /**
     * Set gift from
     *
     * @param float $giftFrom
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsInterface
     */
    public function setGiftFrom($giftFrom){
        return $this->setData(self::GIFT_FROM, $giftFrom);
    }
    /**
     * Get gift to
     *
     * @return float|null
     */
    public function getGiftTo(){
        return $this->getData(self::GIFT_TO);
    }
    /**
     * Set gift to
     *
     * @param float $giftTo
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsInterface
     */
    public function setGiftTo($giftTo){
        return $this->setData(self::GIFT_TO, $giftTo);
    }
    /**
     * Get gift drop down
     *
     * @return string|null
     */
    public function getGiftDropdown(){
        return $this->getData(self::GIFT_DROPDOWN);
    }
    /**
     * Set gift drop down
     *
     * @param string $giftDropdown
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsInterface
     */
    public function setGiftDropdown($giftDropdown){
        return $this->setData(self::GIFT_DROPDOWN, $giftDropdown);
    }
    /**
     * Get gift price type
     *
     * @return int|null
     */
    public function getGiftPriceType(){
        return $this->getData(self::GIFT_PRICE_TYPE);
    }
    /**
     * Set gift price type
     *
     * @param int $giftPriceType
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsInterface
     */
    public function setGiftPriceType($giftPriceType){
        return $this->setData(self::GIFT_PRICE_TYPE, $giftPriceType);
    }
    /**
     * Get gift price
     *
     * @return int|null
     */
    public function getGiftPrice(){
        return $this->getData(self::GIFT_PRICE);
    }
    /**
     * Set gift price
     *
     * @param int $giftPrice
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsInterface
     */
    public function setGiftPrice($giftPrice){
        return $this->setData(self::GIFT_PRICE, $giftPrice);
    }

    /**
     * Get templates
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardTemplateInterface[]
     */
    public function getTemplates(){
        return $this->getData(self::TEMPLATES);
    }
    /**
     * Set gift price
     *
     * @param \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardTemplateInterface[] $templates
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsInterface
     */
    public function setTemplates($templates){
        return $this->setData(self::TEMPLATES, $templates);
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
        \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}