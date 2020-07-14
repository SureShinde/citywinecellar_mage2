<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Payment;

/**
 * Class Magestore\Webpos\Model\Payment\Payment
 *
 */
class Payment extends \Magento\Framework\Model\AbstractModel implements
    \Magestore\Webpos\Api\Data\Payment\PaymentInterface
{
    /**
     * Set code
     *
     * @api
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * Get code
     *
     * @api
     * @return string
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * Set title
     *
     * @api
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Get title
     *
     * @api
     * @return string|null
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }


    /**
     * Set type
     *
     * @api
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Get type
     *
     * @api
     * @return string
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }
    /**
     * Get is default
     *
     * @api
     * @return string|null
     */
    public function getIsDefault()
    {
        return $this->getData(self::IS_DEFAULT);
    }

    /**
     * Set is default
     *
     * @api
     * @param string $isDefault
     * @return $this
     */
    public function setIsDefault($isDefault)
    {
        return $this->setData(self::IS_DEFAULT, $isDefault);
    }

    /**
     * Get is pay later
     *
     * @api
     * @return string|null
     */
    public function getIsPayLater()
    {
        return $this->getData(self::IS_PAY_LATER);
    }

    /**
     * Set is pay later
     *
     * @api
     * @param string $isPayLater
     * @return $this
     */
    public function setIsPayLater($isPayLater)
    {
        return $this->setData(self::IS_PAY_LATER, $isPayLater);
    }

    /**
     * Get is reference number
     *
     * @api
     * @return string|null
     */
    public function getIsReferenceNumber()
    {
        return $this->getData(self::IS_REFERENCE_NUMBER);
    }

    /**
     * Set is reference number
     *
     * @api
     * @param string $isReferenceNumber
     * @return $this
     */
    public function setIsReferenceNumber($isReferenceNumber)
    {
        return $this->setData(self::IS_REFERENCE_NUMBER, $isReferenceNumber);
    }

    /**
     * Get is suggest money
     *
     * @api
     * @return int
     */
    public function getIsSuggestMoney(){
        return $this->getData(self::IS_SUGGEST_MONEY);
    }

    /**
     * Set is suggest money
     *
     * @api
     * @param int $isSuggestMoney
     * @return $this
     */
    public function setIsSuggestMoney($isSuggestMoney){
        return $this->setData(self::IS_SUGGEST_MONEY, $isSuggestMoney);
    }
    /**
     * Get int can due
     *
     * @api
     * @return int
     */
    public function getCanDue(){
        return $this->getData(self::CAN_DUE);
    }

    /**
     * Set is can due
     *
     * @api
     * @param int $canDue
     * @return $this
     */
    public function setCanDue($canDue){
        return $this->setData(self::CAN_DUE, $canDue);
    }
    /**
     * Get int isAllowPayViaEmail
     *
     * @api
     * @return int
     */
    public function getIsAllowPayViaEmail() {
        return $this->getData(self::IS_ALLOW_PAY_VIA_EMAIL);
    }

    /**
     * Set is $isAllowPayViaEmail
     *
     * @api
     * @param int $isAllowPayViaEmail
     * @return $this
     */
    public function setIsAllowPayViaEmail($isAllowPayViaEmail) {
        return $this->setData(self::IS_ALLOW_PAY_VIA_EMAIL, $isAllowPayViaEmail);
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
        \Magestore\Webpos\Api\Data\Payment\PaymentExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
