<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PaymentOffline\Api\Data;

interface PaymentOfflineInterface {
    const PAYMENT_OFFLINE_ID = 'payment_offline_id';
    const ENABLE = 'enable';
    const TITLE = 'title';
    const USE_REFERENCE_NUMBER = 'use_reference_number';
    const ICON_TYPE = 'icon_type';
    const ICON_PATH = 'icon_path';
    const USE_PAY_LATER = 'use_pay_later';
    const SORT_ORDER = 'sort_order';
    const PAYMENT_CODE = 'payment_code';

    /**
     * Get Payment Offline Id
     *
     * @return int|null
     */
    public function getPaymentOfflineId();
    /**
     * Set Location Id
     *
     * @param int $paymentOfflineId
     * @return PaymentOfflineInterface
     */
    public function setPaymentOfflineId($paymentOfflineId);

    /**
     * Get Enable
     *
     * @return int|null
     */
    public function getEnable();
    /**
     * Set Enable
     *
     * @param int $enable
     * @return PaymentOfflineInterface
     */
    public function setEnable($enable);
    /**
     * Get title
     *
     * @api
     * @return string
     */
    public function getTitle();

    /**
     * Set title
     *
     * @api
     * @param string $title
     * @return PaymentOfflineInterface
     */
    public function setTitle($title);

    /**
     * Get use reference number
     *
     * @return int|null
     */
    public function getUseReferenceNumber();
    /**
     * Set use reference number
     *
     * @param int $useReferenceNumber
     * @return PaymentOfflineInterface
     */
    public function setUseReferenceNumber($useReferenceNumber);

    /**
     * Get Icon Type
     *
     * @return int|null
     */
    public function getIconType();
    /**
     * Set Icon Type
     *
     * @param int|null $iconType
     * @return PaymentOfflineInterface
     */
    public function setIconType($iconType);

    /**
     * Get Icon Path
     *
     * @return string
     */
    public function getIconPath();
    /**
     * Set Icon Path
     *
     * @param string $iconPath
     * @return PaymentOfflineInterface
     */
    public function setIconPath($iconPath);

    /**
     * Get use pay later
     *
     * @return int|null
     */
    public function getUsePayLater();
    /**
     * Set use pay later
     *
     * @param int|null $usePayLater
     * @return PaymentOfflineInterface
     */
    public function setUsePayLater($usePayLater);

    /**
     * Get sort order
     *
     * @return int|null
     */
    public function getSortOrder();
    /**
     * Set sort order
     *
     * @param int|null $sortOrder
     * @return PaymentOfflineInterface
     */
    public function setSortOrder($sortOrder);

    /**
     * Get Payment Code
     *
     * @return string
     */
    public function getPaymentCode();

    /**
     * Set Payment Code
     *
     * @param string $paymentCode
     * @return PaymentOfflineInterface
     */
    public function setPaymentCode($paymentCode);
}