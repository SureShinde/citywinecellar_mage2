<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Catalog\Option;

/**
 * Interface ConfigOptionsInterface
 */
interface GiftCardPriceOptionsInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const GIFT_CARD_TYPE = 'gift_card_type';
    const GIFT_TYPE = 'gift_type';
    const GIFT_VALUE = 'gift_value';
    const GIFT_FROM = 'gift_from';
    const GIFT_TO = 'gift_to';
    const GIFT_DROPDOWN = 'gift_dropdown';
    const GIFT_PRICE_TYPE = 'gift_price_type';
    const GIFT_PRICE = 'gift_price';
    const TEMPLATES = 'templates';
    /**
     * Get gift price type
     *
     * @return int|null
     */
    public function getGiftCardType();
    /**
     * Set gift price type
     *
     * @param int $giftCardType
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsInterface
     */
    public function setGiftCardType($giftCardType);
    /**
     * Get gift type
     *
     * @return int|null
     */
    public function getGiftType();
    /**
     * Set gift type
     *
     * @param int $giftType
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsInterface
     */
    public function setGiftType($giftType);
    /**
     * Get gift value
     *
     * @return float|null
     */
    public function getGiftValue();
    /**
     * Set gift value
     *
     * @param float $giftValue
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsInterface
     */
    public function setGiftValue($giftValue);
    /**
     * Get gift from
     *
     * @return float|null
     */
    public function getGiftFrom();
    /**
     * Set gift from
     *
     * @param float $giftFrom
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsInterface
     */
    public function setGiftFrom($giftFrom);
    /**
     * Get gift to
     *
     * @return float|null
     */
    public function getGiftTo();
    /**
     * Set gift to
     *
     * @param float $giftTo
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsInterface
     */
    public function setGiftTo($giftTo);
    /**
     * Get gift drop down
     *
     * @return string|null
     */
    public function getGiftDropdown();
    /**
     * Set gift drop down
     *
     * @param string $giftDropdown
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsInterface
     */
    public function setGiftDropdown($giftDropdown);
    /**
     * Get gift price type
     *
     * @return int|null
     */
    public function getGiftPriceType();
    /**
     * Set gift price type
     *
     * @param int $giftPriceType
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsInterface
     */
    public function setGiftPriceType($giftPriceType);
    /**
     * Get gift price
     *
     * @return string|int|null
     */
    public function getGiftPrice();
    /**
     * Set gift price
     *
     * @param string|int $giftPrice
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsInterface
     */
    public function setGiftPrice($giftPrice);
    /**
     * Get templates
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardTemplateInterface[]
     */
    public function getTemplates();
    /**
     * Set gift price
     *
     * @param \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardTemplateInterface[] $templates
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsInterface
     */
    public function setTemplates($templates);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsExtensionInterface $extensionAttributes
    );

}
