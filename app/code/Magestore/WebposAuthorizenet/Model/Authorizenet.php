<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposAuthorizenet\Model;

use Magento\Framework\Exception\StateException;
use \net\authorize\api\contract\v1 as apiContract;
use \net\authorize\api\controller as apiController;
use \net\authorize\api\constants as apiConstants;

/**
 * Class Authorizenet
 *
 * Used for authorize net model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Authorizenet implements \Magestore\WebposAuthorizenet\Api\AuthorizenetInterface
{
    const PAYMENT_METHOD = 'authorizenet';

    /**
     * @var \Magestore\WebposAuthorizenet\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * Authorizenet constructor.
     * @param \Magestore\WebposAuthorizenet\Helper\Data $helper
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     */
    public function __construct(
        \Magestore\WebposAuthorizenet\Helper\Data $helper,
        \Magento\Quote\Model\QuoteRepository $quoteRepository
    ) {
        $this->helper = $helper;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Validate Required SDK
     *
     * @return bool
     */
    public function validateRequiredSDK()
    {
        return (class_exists("\\net\\authorize\\api\\contract\\v1\\MerchantAuthenticationType")) ? true
            : false;
    }

    /**
     * Get Config
     *
     * @param string $key
     * @return array
     */
    public function getConfig($key = '')
    {
        $configs = $this->helper->getAuthorizenetConfig();
        return ($key) ? $configs[$key] : $configs;
    }

    /**
     * Get quote by id
     *
     * @param string $quoteId
     * @return \Magento\Quote\Api\Data\CartInterface
     */
    public function getQuoteById($quoteId)
    {
        $quote = $this->quoteRepository->get($quoteId);
        return $quote;
    }

    /**
     * Complete Payment
     *
     * @param string $token
     * @param string $amount
     * @return string
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function completePayment($token, $amount)
    {
        /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
        // phpstan:ignore
        $merchantAuthentication = new apiContract\MerchantAuthenticationType();
        $merchantAuthentication->setName($this->getConfig('api_login'));
        $merchantAuthentication->setTransactionKey($this->getConfig('transaction_key'));

        // Set the transaction's refId
        $refId = 'ref' . time();
        // Create the payment object for a payment nonce
        // phpstan:ignore
        $opaqueData = new apiContract\OpaqueDataType();
        $opaqueData->setDataDescriptor("COMMON.ACCEPT.INAPP.PAYMENT");
        $opaqueData->setDataValue($token);

        // Add the payment data to a paymentType object
        // phpstan:ignore
        $paymentOne = new apiContract\PaymentType();
        $paymentOne->setOpaqueData($opaqueData);
        // Create order information
        // phpstan:ignore
        $order = new apiContract\OrderType();
        $order->setInvoiceNumber('POS-' . time());
        $order->setDescription(__('Payment for POS'));

        //Add values for transaction settings
        // phpstan:ignore
        $duplicateWindowSetting = new apiContract\SettingType();
        $duplicateWindowSetting->setSettingName("duplicateWindow");
        $duplicateWindowSetting->setSettingValue("600");
        // Create a transactionRequestType object and add the previous objects to it
        $type = 'authOnlyTransaction';
        if ($this->getConfig('payment_action') == \Magento\Payment\Model\MethodInterface::ACTION_AUTHORIZE_CAPTURE) {
            $type = 'authCaptureTransaction';
        }
        // phpstan:ignore
        $transactionRequestType = new apiContract\TransactionRequestType();
        $transactionRequestType->setTransactionType($type);
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);
        // Assemble the complete transaction request
        // phpstan:ignore
        $request = new apiContract\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);
        // Create the controller and get the response
        // phpstan:ignore
        $controller = new apiController\CreateTransactionController($request);
        if ($this->getConfig('is_sandbox') == "1") {
            // phpstan:ignore
            $apiUrl = apiConstants\ANetEnvironment::SANDBOX;
        } else {
            // phpstan:ignore
            $apiUrl = apiConstants\ANetEnvironment::PRODUCTION;
        }
        $response = $controller->executeWithApiResponse($apiUrl);
        $result = '';
        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == 'Ok') {
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getMessages() != null) {
                    $result = $tresponse->getTransId();
                } else {
                    if ($tresponse != null && $tresponse->getErrors() != null) {
                        if ($tresponse->getErrors()[0]->getErrorCode() == 'E00007') {
                            throw new StateException(
                                __('Connection failed. Please contact admin to check the configuration of API.')
                            );
                        }
                    }
                    throw new StateException(__('Transaction is failed'));
                }
                // Or, print errors if the API request wasn't successful
            } else {
                $tresponse = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getErrors() != null) {
                    if ($tresponse->getErrors()[0]->getErrorCode() == 'E00007') {
                        throw new StateException(
                            __('Connection failed. Please contact admin to check the configuration of API.')
                        );
                    }
                } elseif ($response != null && $response->getMessages() != null) {
                    if ($response->getMessages()->getMessage()[0]->getCode() == 'E00003') {
                        throw new StateException(
                            __('Connection failed. Please contact admin to check the configuration of API.')
                        );
                    }
                }
                throw new StateException(__('Transaction is failed'));
            }
        } else {
            throw new StateException(__('Transaction is failed'));
        }
        return $result;
    }

    /**
     * Test connect authorizenet API
     *
     * @return bool
     * @throws \Exception
     */
    public function canConnectToApi()
    {
        if ($this->testCreate()) {
            return true;
        }
        return false;
    }

    /**
     * Test create authorizenet payment
     *
     * @throws \Exception
     * phpcs:disable Generic.Metrics.NestingLevel
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testCreate()
    {
        try {
            /* Create a merchantAuthenticationType object with authentication details
           retrieved from the constants file */
            // phpstan:ignore
            $merchantAuthentication = new apiContract\MerchantAuthenticationType();
//            $merchantAuthentication->setName('5KP3u95bQpv');
//            $merchantAuthentication->setTransactionKey('346HZ32z3fP4hTG2');
            $merchantAuthentication->setName($this->getConfig('api_login'));
            $merchantAuthentication->setTransactionKey($this->getConfig('transaction_key'));

            // Set the transaction's refId
            $refId = 'ref' . time();
            // Create the payment data for a credit card
            // phpstan:ignore
            $creditCard = new apiContract\CreditCardType();
            $creditCard->setCardNumber("4111111111111111");
            $creditCard->setExpirationDate("1226");
            $creditCard->setCardCode("123");
            // phpstan:ignore
            $paymentOne = new apiContract\PaymentType();
            $paymentOne->setCreditCard($creditCard);
            // phpstan:ignore
            $order = new apiContract\OrderType();
            $order->setDescription("New Item");
            // Set the customer's Bill To address
            // phpstan:ignore
            $customerAddress = new apiContract\CustomerAddressType();
            $customerAddress->setFirstName("Ellen");
            $customerAddress->setLastName("Johnson");
            $customerAddress->setCompany("Souveniropolis");
            $customerAddress->setAddress("14 Main Street");
            $customerAddress->setCity("Pecan Springs");
            $customerAddress->setState("TX");
            $customerAddress->setZip("44628");
            $customerAddress->setCountry("USA");
            // Set the customer's identifying information
            // phpstan:ignore
            $customerData = new apiContract\CustomerDataType();
            $customerData->setType("individual");
            $customerData->setId(time());
            $customerData->setEmail("EllenJohnson@example.com");
            //Add values for transaction settings
            // phpstan:ignore
            $duplicateWindowSetting = new apiContract\SettingType();
            $duplicateWindowSetting->setSettingName("duplicateWindow");
            $duplicateWindowSetting->setSettingValue("600");
            // Create a TransactionRequestType object
            // phpstan:ignore
            $transactionRequestType = new apiContract\TransactionRequestType();
            $transactionRequestType->setTransactionType("authOnlyTransaction");
            $transactionRequestType->setAmount(100);
            $transactionRequestType->setOrder($order);
            $transactionRequestType->setPayment($paymentOne);
            $transactionRequestType->setBillTo($customerAddress);
            $transactionRequestType->setCustomer($customerData);
            $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);
            // phpstan:ignore
            $request = new apiContract\CreateTransactionRequest();
            $request->setMerchantAuthentication($merchantAuthentication);
            $request->setRefId($refId);
            $request->setTransactionRequest($transactionRequestType);
            // phpstan:ignore
            $controller = new apiController\CreateTransactionController($request);
            $apiUrl = "";
            if ($this->getConfig('is_sandbox') == "1") {
                // phpstan:ignore
                $apiUrl = apiConstants\ANetEnvironment::SANDBOX;
            } else {
                // phpstan:ignore
                $apiUrl = apiConstants\ANetEnvironment::PRODUCTION;
            }
            $response = $controller->executeWithApiResponse($apiUrl);
            if ($response != null) {
                if ($response->getMessages()->getResultCode() == 'Ok') {
                    $tresponse = $response->getTransactionResponse();

                    if ($tresponse != null && $tresponse->getMessages() != null) {
                        return true;
                    } else {
                        if ($tresponse != null && $tresponse->getErrors() != null) {
                            if ($tresponse->getErrors()[0]->getErrorCode() == 'E00007') {
                                throw new StateException(
                                    __('Connection failed. Please contact admin to check the configuration of API.')
                                );
                            }
                        }
                        return false;
                    }
                } else {
                    $tresponse = $response->getTransactionResponse();
                    if ($tresponse != null && $tresponse->getErrors() != null) {
                        if ($tresponse->getErrors()[0]->getErrorCode() == 'E00007') {
                            throw new StateException(
                                __('Connection failed. Please contact admin to check the configuration of API.')
                            );
                        }
                    }
                    return false;
                }
            }
            return false;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException($e->getMessage());
        }
    }
}
