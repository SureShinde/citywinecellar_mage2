<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Denomination;

/**
 * Interface DenominationInterface
 * @package Magestore\Webpos\Api\Data\Denomination
 */
interface DenominationInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /*#@+
     * Constants defined for keys of data array
     */
    const DENOMINATION_ID = "denomination_id";
    const DENOMINATION_NAME = "denomination_name";
    const DENOMINATION_VALUE = "denomination_value";
    const LOCATION_IDS = "location_ids";
    const SORT_ORDER = "sort_order";

    /**
     *  Get Denomination Id
     * @return int|null
     */
    public function getDenominationId();

    /**
     * Set Denomination Id
     *
     * @param int $denominationId
     * @return $this
     */
    public function setDenominationId($denominationId);

    /**
     *  Get Denomination Name
     * @return string|null
     */
    public function getDenominationName();

    /**
     * Set Denomination Name
     *
     * @param string $denominationName
     * @return $this
     */
    public function setDenominationName($denominationName);

    /**
     *  Get Denomination Value
     * @return float
     */
    public function getDenominationValue();

    /**
     * Set Denomination Value
     *
     * @param string $denominationValue
     * @return $this
     */
    public function setDenominationValue($denominationValue);

    /**
     *  Get Location Ids
     * @return string|null
     */
    public function getLocationIds();

    /**
     * Set Location Ids
     *
     * @param string|null $locationIds
     * @return $this
     */
    public function setLocationIds($locationIds);

    /**
     *  Set Sort Order
     * @return int|null
     */
    public function getSortOrder();

    /**
     * Set Sort Order
     *
     * @param int|null $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Denomination\DenominationExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Denomination\DenominationExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Denomination\DenominationExtensionInterface $extensionAttributes
    );
}