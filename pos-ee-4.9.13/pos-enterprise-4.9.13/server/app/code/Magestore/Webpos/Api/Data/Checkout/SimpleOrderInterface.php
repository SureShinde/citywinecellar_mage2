<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Checkout;

interface SimpleOrderInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ENTITY_ID = 'entity_id';
    const STATE = 'state';
    const STATUS = 'status';
    const GRAND_TOTAL = 'grand_total';
    const TOTAL_PAID = 'total_paid';
    const TOTAL_DUE = 'total_due';
    const INCREMENT_ID = 'increment_id';
    const CREATED_AT = 'created_at';


    /**
     * Get Entity Id
     *
     * @return int|null
     */
    public function getEntityId();
    /**
     * Set Entity Id
     *
     * @param int|null $entityId
     * @return OrderInterface
     */
    public function setEntityId($entityId);

    /**
     * Get State
     *
     * @return string|null
     */
    public function getState();
    /**
     * Set State
     *
     * @param string|null $state
     * @return OrderInterface
     */
    public function setState($state);

    /**
     * Get Status
     *
     * @return string|null
     */
    public function getStatus();
    /**
     * Set Status
     *
     * @param string|null $status
     * @return OrderInterface
     */
    public function setStatus($status);

    /**
     * Get Grand Total
     *
     * @return float|null
     */
    public function getGrandTotal();
    /**
     * Set Grand Total
     *
     * @param float|null $grandTotal
     * @return OrderInterface
     */
    public function setGrandTotal($grandTotal);

    /**
     * Get Total Paid
     *
     * @return float|null
     */
    public function getTotalPaid();
    /**
     * Set Total Paid
     *
     * @param float|null $totalPaid
     * @return OrderInterface
     */
    public function setTotalPaid($totalPaid);

    /**
     * Get Total Due
     *
     * @return float|null
     */
    public function getTotalDue();
    /**
     * Set Total Due
     *
     * @param float|null $totalDue
     * @return OrderInterface
     */
    public function setTotalDue($totalDue);

    /**
     * Get Increment Id
     *
     * @return string|null
     */
    public function getIncrementId();
    /**
     * Set Increment Id
     *
     * @param string|null $incrementId
     * @return OrderInterface
     */
    public function setIncrementId($incrementId);

    /**
     * Get Created At
     *
     * @return string|null
     */
    public function getCreatedAt();
    /**
     * Set Created At
     *
     * @param string|null $createdAt
     * @return OrderInterface
     */
    public function setCreatedAt($createdAt);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\SimpleOrderExtensionInterface|null
     * @since 102.0.0
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\SimpleOrderExtensionInterface $extensionAttributes
     * @return $this
     * @since 102.0.0
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Checkout\SimpleOrderExtensionInterface $extensionAttributes
    );

}