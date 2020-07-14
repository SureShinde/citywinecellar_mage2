<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PaymentOffline\Model;

use Magestore\PaymentOffline\Api\Data\PaymentOfflineInterface;

/**
 * Class PaymentOffline
 * @package Magestore\PaymentOffline\Model
 */
class PaymentOffline extends \Magento\Framework\Model\AbstractModel implements PaymentOfflineInterface
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'webpos_payment_offline';

    /**
     * PaymentOffline constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\PaymentOffline $resource
     * @param ResourceModel\PaymentOffline\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\PaymentOffline\Model\ResourceModel\PaymentOffline $resource,
        \Magestore\PaymentOffline\Model\ResourceModel\PaymentOffline\Collection $resourceCollection,
        array $data = [])
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @inheritdoc
     */
    public function getPaymentOfflineId()
    {
        return $this->getData(self::PAYMENT_OFFLINE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setPaymentOfflineId($paymentOfflineId)
    {
        return $this->setData(self::PAYMENT_OFFLINE_ID, $paymentOfflineId);
    }

    /**
     * @inheritdoc
     */
    public function getEnable()
    {
        return $this->getData(self::ENABLE);
    }

    /**
     * @inheritdoc
     */
    public function setEnable($enable)
    {
        return $this->setData(self::ENABLE, $enable);
    }

    /**
     * @return int|null
     */
    public function getIconType()
    {
        return $this->getData(self::ICON_TYPE);
    }

    /**
     * @param int|null $iconType
     * @return PaymentOfflineInterface
     */
    public function setIconType($iconType)
    {
        return $this->setData(self::ICON_TYPE, $iconType);
    }

    /**
     * @return string
     */
    public function getIconPath()
    {
        return $this->getData(self::ICON_PATH);
    }

    /**
     * @param string $iconPath
     * @return PaymentOfflineInterface
     */
    public function setIconPath($iconPath)
    {
        return $this->setData(self::ICON_PATH, $iconPath);
    }

    /**
     * @return int|null
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * @param int|null $sortOrder
     * @return PaymentOfflineInterface
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @param string $title
     * @return PaymentOfflineInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @return int|null
     */
    public function getUsePayLater()
    {
        return $this->getData(self::USE_PAY_LATER);
    }

    /**
     * @param int|null $usePayLater
     * @return PaymentOfflineInterface
     */
    public function setUsePayLater($usePayLater)
    {
        return $this->setData(self::USE_PAY_LATER, $usePayLater);
    }

    /**
     * @return int|null
     */
    public function getUseReferenceNumber()
    {
        return $this->getData(self::USE_REFERENCE_NUMBER);
    }

    /**
     * @param string $useReferenceNumber
     * @return PaymentOfflineInterface
     */
    public function setUseReferenceNumber($useReferenceNumber)
    {
        return $this->setData(self::USE_REFERENCE_NUMBER, $useReferenceNumber);
    }

    /**
     * @return string
     */
    public function getPaymentCode()
    {
        return $this->getData(self::PAYMENT_CODE);
    }

    /**
     * @param string $paymentCode
     * @return PaymentOfflineInterface
     */
    public function setPaymentCode($paymentCode)
    {
        return $this->setData(self::PAYMENT_CODE, $paymentCode);
    }
}