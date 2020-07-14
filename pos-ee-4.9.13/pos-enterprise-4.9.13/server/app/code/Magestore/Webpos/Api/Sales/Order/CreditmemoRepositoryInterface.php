<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Sales\Order;

/**
 * Interface CreditmemoRepositoryInterface
 *
 * @package Magestore\Webpos\Api\Sales\Order
 */
interface CreditmemoRepositoryInterface
{
    /**
     * Create credit memo bu order id
     *
     * @param \Magestore\Webpos\Api\Data\Sales\Order\CreditmemoInterface $creditmemo
     * @return \Magestore\Webpos\Api\Data\Checkout\OrderInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createCreditmemoByOrderId($creditmemo);

    /**
     * Process creating creditmemo request on action log
     *
     * @param string $requestIncrementId
     * @return \Magestore\Webpos\Api\Data\Checkout\OrderInterface|bool
     */
    public function processCreditmemoRequest($requestIncrementId);

    /**
     * Send email
     *
     * @param string $creditmemoIncrementId
     * @param string $email
     * @param string|null $incrementId
     * @return boolean
     * @throws \Exception
     */
    public function sendEmail($creditmemoIncrementId, $email, $incrementId = '');

    /**
     * Create customer
     *
     * @param \Magestore\Webpos\Api\Data\Customer\CustomerInterface $customer
     * @param string $incrementId
     * @return \Magestore\Webpos\Api\Data\Customer\CustomerInterface
     * @throws \Exception
     */
    public function createCustomer($customer, $incrementId);
}
