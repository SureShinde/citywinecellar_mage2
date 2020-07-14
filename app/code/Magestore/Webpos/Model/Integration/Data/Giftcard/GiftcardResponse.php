<?php

namespace Magestore\Webpos\Model\Integration\Data\Giftcard;

use Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardResponseInterface;

class GiftcardResponse extends \Magento\Framework\DataObject implements GiftcardResponseInterface
{
    /**
     * @return \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardInterface[]|null
     */
    public function getAppliedCodes()
    {
        return $this->getData(self::APPLIED_CODES);
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardInterface[] $appliedCodes
     * @return GiftcardResponseInterface
     */
    public function setAppliedCodes($appliedCodes)
    {
        return $this->setData(self::APPLIED_CODES, $appliedCodes);
    }

    /**
     * @return \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardInterface[]|null
     */
    public function getExistingCodes()
    {
        return $this->getData(self::EXISTING_CODES);
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardInterface[] $existingCodes
     * @return GiftcardResponseInterface
     */
    public function setExistingCodes($existingCodes)
    {
        return $this->setData(self::EXISTING_CODES, $existingCodes);
    }

    /**
     * @return string|null
     */
    public function getError()
    {
        return $this->getData(self::ERROR);
    }

    /**
     * @param  string|null
     * @return GiftcardResponseInterface
     */
    public function setError($error)
    {
        return $this->setData(self::ERROR, $error);
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
        \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardResponseExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}