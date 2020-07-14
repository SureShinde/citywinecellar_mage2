<?php

namespace Magestore\Webpos\Model\Integration\Data\Giftcard;

use Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardInterface;

class Giftcard extends \Magento\Framework\DataObject implements GiftcardInterface
{
    /**
     * @return string
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * @param string $code
     * @return GiftcardInterface
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * @return float
     */
    public function getBalance()
    {
        return $this->getData(self::BALANCE);
    }

    /**
     * @param float $balance
     * @return GiftcardInterface
     */
    public function setBalance($balance)
    {
        return $this->setData(self::BALANCE, $balance);
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->getData(self::CURRENCY);
    }

    /**
     * @param string $currency
     * @return GiftcardInterface
     */
    public function setCurrency($currency)
    {
        return $this->setData(self::CURRENCY, $currency);
    }

    /**
     * Get ValidItemIds
     *
     * @return float[]|null
     */
    public function getValidItemIds()
    {
        return $this->getData(self::VALID_ITEM_IDS);
    }

    /**
     * Set ValidItemIds
     *
     * @param float[]|null $validItemIds
     * @return GiftcardInterface
     */
    public function setValidItemIds($validItemIds)
    {
        return $this->setData(self::VALID_ITEM_IDS, $validItemIds);
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
        \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}