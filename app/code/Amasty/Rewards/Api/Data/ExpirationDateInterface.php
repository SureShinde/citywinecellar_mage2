<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Api\Data;

interface ExpirationDateInterface
{
    /**#@+
     * Constants for expiration behavior
     */
    const EXPIRATION_DEFAULT = 0;
    const EXPIRATION_NEVER = 1;
    const EXPIRATION_CUSTOM = 2;
    /**#@-*/

    /**#@+
     * Constants defined for keys of data array
     */
    const ENTITY_ID = 'entity_id';
    const CUSTOMER_ID = 'customer_id';
    const DATE = 'date';
    const AMOUNT = 'amount';
    /**#@-*/

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     *
     * @return \Amasty\Rewards\Api\Data\ExpirationDateInterface
     */
    public function setEntityId($entityId);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $customerId
     *
     * @return \Amasty\Rewards\Api\Data\ExpirationDateInterface
     */
    public function setCustomerId($customerId);

    /**
     * @return string|null
     */
    public function getDate();

    /**
     * @param string|null $date
     *
     * @return \Amasty\Rewards\Api\Data\ExpirationDateInterface
     */
    public function setDate($date);

    /**
     * @return float
     */
    public function getAmount();

    /**
     * @param float $amount
     *
     * @return \Amasty\Rewards\Api\Data\ExpirationDateInterface
     */
    public function setAmount($amount);
}
