<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Payment;

interface PaymentInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Get items.
     *
     * @return \Magestore\Webpos\Api\Data\Payment\PaymentInterface[] Array of collection items.
     */

    /**#@+
     * Constants for keys of data array
     */
    const CODE = 'code';
    const TITLE = 'title';
    const TYPE = 'type';
    const IS_DEFAULT = 'is_default';
    const IS_PAY_LATER = 'is_pay_later';
    const IS_REFERENCE_NUMBER = 'is_reference_number';
    const IS_SUGGEST_MONEY = 'is_suggest_money';
    const IS_ALLOW_PAY_VIA_EMAIL = 'is_allow_pay_via_email';
    const CAN_DUE = 'can_due';
    const YES = 1;
    const NO = 0;


    const REFUND_TYPE = 1;
    const ORDER_TYPE = 0;
    /**#@-*/

    /**
     * Get code
     *
     * @api
     * @return string
     */
    public function getCode();

    /**
     * Set code
     *
     * @api
     * @param string $code
     * @return PaymentInterface
     */
    public function setCode($code);

    /**
     * Get title
     *
     * @api
     * @return string|null
     */
    public function getTitle();

    /**
     * Set title
     *
     * @api
     * @param string $title
     * @return PaymentInterface
     */
    public function setTitle($title);


    /**
     * Get type
     *
     * @api
     * @return int
     */
    public function getType();

    /**
     * Set type
     *
     * @api
     * @param int $type
     * @return PaymentInterface
     */
    public function setType($type);

    /**
     * Get is default
     *
     * @api
     * @return int
     */
    public function getIsDefault();

    /**
     * Set is default
     *
     * @api
     * @param int $isDefault
     * @return PaymentInterface
     */
    public function setIsDefault($isDefault);

    /**
     * Get is pay later
     *
     * @api
     * @return int
     */
    public function getIsPayLater();

    /**
     * Set is pay later
     *
     * @api
     * @param int $isPayLater
     * @return PaymentInterface
     */
    public function setIsPayLater($isPayLater);

    /**
     * Get is reference number
     *
     * @api
     * @return int
     */
    public function getIsReferenceNumber();

    /**
     * Set is reference number
     *
     * @api
     * @param int $isReferenceNumber
     * @return PaymentInterface
     */
    public function setIsReferenceNumber($isReferenceNumber);

    /**
     * Get is suggest money
     *
     * @api
     * @return int
     */
    public function getIsSuggestMoney();

    /**
     * Set is suggest money
     *
     * @api
     * @param int $isSuggestMoney
     * @return PaymentInterface
     */
    public function setIsSuggestMoney($isSuggestMoney);
    /**
     * Get int can due
     *
     * @api
     * @return int
     */
    public function getCanDue();

    /**
     * Set is can due
     *
     * @api
     * @param int $canDue
     * @return PaymentInterface
     */
    public function setCanDue($canDue);
    /**
     * Get int isAllowPayViaEmail
     *
     * @api
     * @return int
     */
    public function getIsAllowPayViaEmail();

    /**
     * Set is $isAllowPayViaEmail
     *
     * @api
     * @param int $isAllowPayViaEmail
     * @return PaymentInterface
     */
    public function setIsAllowPayViaEmail($isAllowPayViaEmail);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Payment\PaymentExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Payment\PaymentExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Payment\PaymentExtensionInterface $extensionAttributes
    );
}
