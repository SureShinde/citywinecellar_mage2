<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Integration\Giftcard;

/**
 * Interface GiftcardResponseInterface
 * @package Magestore\Webpos\Api\Data\Integration\Giftcard
 */
interface GiftcardResponseInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /*#@+
     * Constants defined for keys of data array
     */
    const APPLIED_CODES = "applied_codes";
    const EXISTING_CODES = "existing_codes";
    const ERROR = "error";

    /**
     * @return \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardInterface[]|null
     */
    public function getAppliedCodes();

    /**
     * @param \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardInterface[] $appliedCodes
     * @return GiftcardResponseInterface
     */
    public function setAppliedCodes($appliedCodes);

    /**
     * @return \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardInterface[]|null
     */
    public function getExistingCodes();

    /**
     * @param \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardInterface[] $existingCodes
     * @return GiftcardResponseInterface
     */
    public function setExistingCodes($existingCodes);

    /**
     * @return string|null
     */
    public function getError();

    /**
     * @param  string|null
     * @return GiftcardResponseInterface
     */
    public function setError($error);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardResponseExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardResponseExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardResponseExtensionInterface $extensionAttributes
    );
}