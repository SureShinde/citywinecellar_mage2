<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposPaynl\Api;

/**
 * Service PaypalInterface
 */
interface PaynlInterface
{
    /**
     * ValidateRequiredSDK
     *
     * @return bool
     */
    public function validateRequiredSDK();

    /**
     * GetApiContext
     *
     * @return \PayPal\Rest\ApiContext
     */
    public function getApiContext();

    /**
     * CreatePayment
     *
     * @param string $successUrl
     * @param string $cancelUrl
     * @param \PayPal\Api\Transaction[] $transactions
     * @return string
     * @throws \Exception
     */
    public function createPayment($successUrl, $cancelUrl, $transactions);

    /**
     * CreateTransaction
     *
     * @param string $subtotal
     * @param string $shipping
     * @param string $tax
     * @param string $total
     * @param string $currencyCode
     * @param string $description
     * @return \PayPal\Api\Transaction
     */
    public function createTransaction($subtotal, $shipping, $tax, $total, $currencyCode, $description = '');

    /**
     * CompletePayment
     *
     * @param string $paymentId
     * @param string $payerId
     * @return string
     * @throws \Exception
     */
    public function completePayment($paymentId, $payerId);

    /**
     * CompleteAppPayment
     *
     * @param string $paymentId
     * @return string
     * @throws \Exception
     */
    public function completeAppPayment($paymentId);

    /**
     * CanConnectToApi
     *
     * @return bool
     */
    public function canConnectToApi();

    /**
     * CreateInvoiceObject
     *
     * @param \PayPal\Api\MerchantInfo $merchantInfo
     * @param \PayPal\Api\BillingInfo $billingInfo
     * @param \PayPal\Api\ShippingInfo $shippingInfo
     * @param \PayPal\Api\PaymentTerm $paymentTerm
     * @param \PayPal\Api\InvoiceItem[] $items
     * @param string $note
     * @return \PayPal\Api\Invoice
     * @throws \Exception
     */
    public function createInvoiceObject($merchantInfo, $billingInfo, $shippingInfo, $paymentTerm, $items, $note = '');

    /**
     * CreateInvoice
     *
     * @param \PayPal\Api\Invoice $invoice
     * @return mixed
     * @throws \Exception
     */
    public function createInvoice($invoice);

    /**
     * CreateInvoiceAndSend
     *
     * @param \PayPal\Api\Invoice $invoice
     * @return mixed
     * @throws \Exception
     */
    public function createInvoiceAndSend($invoice);

    /**
     * SendInvoice
     *
     * @param \PayPal\Api\Invoice $invoice
     * @return \Magestore\WebposPaynl\Model\Paypal
     * @throws \Exception
     */
    public function sendInvoice($invoice);

    /**
     * SendInvoiceById
     *
     * @param string $invoiceId
     * @return \Magestore\WebposPaynl\Model\Paypal
     * @throws \Exception
     */
    public function sendInvoiceById($invoiceId);

    /**
     * GetInvoiceQrCode
     *
     * @param \PayPal\Api\Invoice $invoice
     * @return \PayPal\Api\Image
     * @throws \Exception
     */
    public function getInvoiceQrCode($invoice);

    /**
     * CreateMerchantInfo
     *
     * @param string $email
     * @param string $firstname
     * @param string $lastname
     * @param string $businessName
     * @param \PayPal\Api\Phone $phone
     * @param \PayPal\Api\Address $address
     * @return \PayPal\Api\MerchantInfo
     */
    public function createMerchantInfo($email, $firstname, $lastname, $businessName, $phone, $address);

    /**
     * CreatePhone
     *
     * @param string $countryCode
     * @param string $number
     * @return \PayPal\Api\Phone
     */
    public function createPhone($countryCode, $number);

    /**
     * CreateAddress
     *
     * @param string $line1
     * @param string $line2
     * @param string $city
     * @param string $state
     * @param string $postalCode
     * @param string $countryCode
     * @return \PayPal\Api\Address
     */
    public function createAddress($line1, $line2, $city, $state, $postalCode, $countryCode);

    /**
     * CreateBillingInfo
     *
     * @param string $email
     * @param string $firstname
     * @param string $lastname
     * @param string $businessName
     * @param \PayPal\Api\Phone $phone
     * @param string $addtionalInfo
     * @param \PayPal\Api\InvoiceAddress $invoiceAddress
     * @return \PayPal\Api\BillingInfo
     */
    public function createBillingInfo(
        $email,
        $firstname,
        $lastname,
        $businessName,
        $phone,
        $addtionalInfo,
        $invoiceAddress
    );

    /**
     * CreateInvoiceAddress
     *
     * @param string $line1
     * @param string $line2
     * @param string $city
     * @param string $state
     * @param string $postalCode
     * @param string $countryCode
     * @return \PayPal\Api\InvoiceAddress
     */
    public function createInvoiceAddress($line1, $line2, $city, $state, $postalCode, $countryCode);

    /**
     * CreateShippingInfo
     *
     * @param string $firstname
     * @param string $lastname
     * @param string $businessName
     * @param \PayPal\Api\Phone $phone
     * @param \PayPal\Api\InvoiceAddress $invoiceAddress
     * @return \PayPal\Api\ShippingInfo
     */
    public function createShippingInfo($firstname, $lastname, $businessName, $phone, $invoiceAddress);

    /**
     * CreatePaymentTerm
     *
     * @param string $termType
     * @param string $dueDate
     * @return \PayPal\Api\PaymentTerm
     */
    public function createPaymentTerm($termType, $dueDate);

    /**
     * CreatePercentCost
     *
     * @param string $percent
     * @return \PayPal\Api\Cost
     */
    public function createPercentCost($percent);

    /**
     * CreateFixedCost
     *
     * @param \PayPal\Api\Currency $amount
     * @return \PayPal\Api\Cost
     */
    public function createFixedCost($amount);

    /**
     * CreateCurrency
     *
     * @param string $currencyCode
     * @param string|float $value
     * @return \PayPal\Api\Currency
     */
    public function createCurrency($currencyCode, $value);

    /**
     * CreatePercentTax
     *
     * @param string $percent
     * @param string $name
     * @return \PayPal\Api\Tax
     */
    public function createPercentTax($percent, $name = '');

    /**
     * CreateFixedTax
     *
     * @param \PayPal\Api\Currency $amount
     * @param string $name
     * @return \PayPal\Api\Tax
     */
    public function createFixedTax($amount, $name = '');

    /**
     * CreateInvoiceItem
     *
     * @param string $name
     * @param string $qty
     * @param \PayPal\Api\Currency $unitPrice
     * @return \PayPal\Api\InvoiceItem
     */
    public function createInvoiceItem($name, $qty, $unitPrice);

    /**
     * CreatePaymentSummary
     *
     * @param \PayPal\Api\Currency $other
     * @return \PayPal\Api\PaymentSummary
     */
    public function createPaymentSummary($other);

    /**
     * CreateShippingCost
     *
     * @param \PayPal\Api\Currency $amount
     * @param \PayPal\Api\Tax $tax
     * @return \PayPal\Api\ShippingCost
     */
    public function createShippingCost($amount, $tax);

    /**
     * GetInvoice
     *
     * @param string $invoiceId
     * @return \PayPal\Api\Invoice
     * @throws \Exception
     */
    public function getInvoice($invoiceId);

    /**
     * CreatePaymentDetail
     *
     * @param \PayPal\Api\Currency $amount
     * @param string $note
     * @param string $method
     * @return \PayPal\Api\PaymentDetail
     */
    public function createPaymentDetail($amount, $note = "", $method = "OTHER");

    /**
     * RecordPaymentForInvoice
     *
     * @param \PayPal\Api\Invoice $invoice
     * @param \PayPal\Api\PaymentDetail $paymentDetail
     * @return $this
     * @throws \Exception
     */
    public function recordPaymentForInvoice($invoice, $paymentDetail);

    /**
     * Get access token
     *
     * @return string
     * @throws \Exception
     */
    public function getAccessToken();
}
