<?php

namespace Magestore\Webpos\Api\Checkout;

/**
 * Interface CheckoutRepositoryInterface
 *
 * @package Magestore\Webpos\Api\Checkout
 */
interface CheckoutRepositoryInterface
{
    /**
     * Send email
     *
     * @param \Magento\Sales\Model\Order $order
     * @return boolean
     */
    public function sendEmailOrder(\Magento\Sales\Model\Order $order);

    /**
     * Place order
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\OrderInterface $order
     * @param int $create_shipment
     * @param int $create_invoice
     * @return \Magestore\Webpos\Api\Data\Checkout\OrderInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function placeOrder(
        \Magestore\Webpos\Api\Data\Checkout\OrderInterface $order,
        $create_shipment,
        $create_invoice
    );

    /**
     * Take payment
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\Order\PaymentInterface[] $payments
     * @param string $incrementId
     * @param int $createInvoice
     * @param string $requestIncrementId
     * @return \Magestore\Webpos\Api\Data\Checkout\OrderInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function takePayment($payments, $incrementId, $createInvoice, $requestIncrementId);

    /**
     * Process take payment action log
     *
     * @param string $requestIncrementId
     * @return \Magestore\Webpos\Api\Data\Checkout\OrderInterface|bool
     */
    public function processTakePaymentActionLog($requestIncrementId);

    /**
     * Hold order
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\OrderInterface $order
     * @return \Magestore\Webpos\Api\Data\Checkout\OrderInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function holdOrder(\Magestore\Webpos\Api\Data\Checkout\OrderInterface $order);
}
