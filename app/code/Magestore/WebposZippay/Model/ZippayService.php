<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposZippay\Model;

use Magestore\WebposZippay\Api\Data\ZippayErrorInterface;
use Magestore\WebposZippay\Api\Data\ZippayPurchaseResponseInterface;
use Magestore\WebposZippay\Api\Data\ZippayResponseInterface;

/**
 * Model ZippayService
 */
class ZippayService implements \Magestore\WebposZippay\Api\ZippayServiceInterface
{
    const UNKNOWN_EXCEPTION_MESSAGE = 'Connection failed. Please contact admin to check the configuration of API';
    const STORE_LOCATION_EXCEPTION_MESSAGE = 'Store location is invalid';
    const TIME_OUT_EXCEPTION_MESSAGE = 'The order has timed out. Please start the process again.';
    const TIMEOUT_STATUS = 'Timeout';

    /**
     * @var \Magestore\WebposZippay\Helper\Data
     */
    protected $zippay;

    /**
     * @var \Magestore\WebposZippay\Model\Data\ZippayErrorFactory
     */
    protected $zippayErrorFactory;

    /**
     * @var \Magestore\WebposZippay\Model\Data\ZippayErrorFieldFactory
     */
    protected $zippayErrorFieldFactory;

    /**
     * @var \Magestore\WebposZippay\Model\Data\ZippayPurchaseResponseFactory
     */
    protected $zippayPurchaseResponseFactory;

    /**
     * @var \Magestore\WebposZippay\Model\Data\ZippayResponseFactory
     */
    protected $zippayResponseFactory;

    /**
     * ZippayService constructor.
     *
     * @param \Magestore\WebposZippay\Helper\Data $zippay
     * @param Data\ZippayErrorFactory $zippayErrorFactory
     * @param Data\ZippayErrorFieldFactory $zippayErrorFieldFactory
     * @param Data\ZippayPurchaseResponseFactory $zippayPurchaseResponseFactory
     * @param Data\ZippayResponseFactory $zippayResponseFactory
     */
    public function __construct(
        \Magestore\WebposZippay\Helper\Data $zippay,
        \Magestore\WebposZippay\Model\Data\ZippayErrorFactory $zippayErrorFactory,
        \Magestore\WebposZippay\Model\Data\ZippayErrorFieldFactory $zippayErrorFieldFactory,
        \Magestore\WebposZippay\Model\Data\ZippayPurchaseResponseFactory $zippayPurchaseResponseFactory,
        \Magestore\WebposZippay\Model\Data\ZippayResponseFactory $zippayResponseFactory
    ) {
        $this->zippay = $zippay;
        $this->zippayErrorFactory = $zippayErrorFactory;
        $this->zippayErrorFieldFactory = $zippayErrorFieldFactory;
        $this->zippayPurchaseResponseFactory = $zippayPurchaseResponseFactory;
        $this->zippayResponseFactory = $zippayResponseFactory;
    }

    /**
     * Is Enable
     *
     * @return bool
     */
    public function isEnable()
    {
        $configs = $this->zippay->getZippayConfig();
        return $configs['enable'] && !empty($configs['api_url']) && !empty($configs['api_key']) ? true : false;
    }

    /**
     * Get Configuration Error
     *
     * @return string
     */
    public function getConfigurationError()
    {
        $message = '';
        $configs = $this->zippay->getZippayConfig();
        if ($configs['enable']) {
            if (empty($configs['api_url']) || empty($configs['api_key'])) {
                $message = __('Zippay application api url and api key are required');
            }
        } else {
            $message = __('Zippay integration is disabled');
        }
        return $message;
    }

    /**
     * Get Zip Location Id
     *
     * @param int $webposLocationId
     * @return bool|false|int|string
     */
    private function getZipLocationId($webposLocationId)
    {
        $locationList = json_decode($this->zippay->getLocation(), true);

        if (!is_array($locationList) || empty($locationList)) {
            return false;
        }

        $locationList = array_values($locationList);
        $zipLocationIndex = array_search($webposLocationId, array_column($locationList, 'webpos_location'));

        if (empty($locationList[$zipLocationIndex]) || empty($locationList[$zipLocationIndex]['location_id'])) {
            return false;
        }

        return $locationList[$zipLocationIndex]['location_id'];
    }

    /**
     * Test Api
     *
     * @param string $api_url
     * @param string $api_key
     * @return array
     */
    private function testApi($api_url, $api_key)
    {
        try {
            $curl = curl_init(); // phpcs:ignore Magento2.Functions.DiscouragedFunction

            curl_setopt_array( // phpcs:ignore Magento2.Functions.DiscouragedFunction
                $curl,
                [
                    CURLOPT_URL => $api_url . "/purchaserequests",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_POSTFIELDS => "{}",
                    CURLOPT_HTTPHEADER => [
                        "authorization: Basic " . base64_encode($api_key . ":"),
                        "content-type: application/json"
                    ],
                ]
            );

            $response = curl_exec($curl); // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE); // phpcs:ignore Magento2.Functions.DiscouragedFunction
            curl_error($curl); // phpcs:ignore Magento2.Functions.DiscouragedFunction

            curl_close($curl); // phpcs:ignore Magento2.Functions.DiscouragedFunction
        } catch (\Exception $e) {
            return [
                'httpCode' => 500,
                'response' => false
            ];
        }

        // time out
        if ($httpCode === 0) {
            return [
                'httpCode' => $httpCode,
                'response' => '{
                    "message": "' . self::TIME_OUT_EXCEPTION_MESSAGE . '"
                }'
            ];
        }

        return [
            'httpCode' => $httpCode,
            'response' => $response
        ];
    }

    /**
     * Call Api
     *
     * @param string $endpoint
     * @param string $method
     * @param string $param
     * @param int $timeout
     * @return array
     */
    private function callApi($endpoint, $method = "GET", $param = "{}", $timeout = 30)
    {
        if (!$timeout) {
            return [
                'httpCode' => 0,
                'response' => '{
                    "message": "' . self::TIME_OUT_EXCEPTION_MESSAGE . '"
                }'
            ];
        }

        try {
            $curl = curl_init(); // phpcs:ignore Magento2.Functions.DiscouragedFunction

            curl_setopt_array( // phpcs:ignore Magento2.Functions.DiscouragedFunction
                $curl,
                [
                    CURLOPT_URL => $this->zippay->getApiUrl() . $endpoint,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => $timeout,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => $method,
                    CURLOPT_POSTFIELDS => $param,
                    CURLOPT_HTTPHEADER => [
                        "authorization: Basic " . base64_encode($this->zippay->getApiKey() . ":"),
                        "content-type: application/json"
                    ],
                ]
            );

            $response = curl_exec($curl); // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE); // phpcs:ignore Magento2.Functions.DiscouragedFunction
            curl_error($curl); // phpcs:ignore Magento2.Functions.DiscouragedFunction

            curl_close($curl); // phpcs:ignore Magento2.Functions.DiscouragedFunction
        } catch (\Exception $e) {
            return [
                'httpCode' => 500,
                'response' => false
            ];
        }

        // time out
        if ($httpCode === 0) {
            return [
                'httpCode' => $httpCode,
                'response' => '{
                    "message": "' . self::TIME_OUT_EXCEPTION_MESSAGE . '"
                }'
            ];
        }

        return [
            'httpCode' => $httpCode,
            'response' => $response
        ];
    }

    /**
     * Can Connect To Api
     *
     * @param null|string $apiUrl
     * @param null|string $apiKey
     * @return bool
     */
    public function canConnectToApi($apiUrl = null, $apiKey = null)
    {
        try {
            /**
             * Test api
             */
            if ($apiUrl && $apiKey) {
                $testConnect = $this->testApi($apiUrl, $apiKey);
            } else {
                $testConnect = $this->callApi("/purchaserequests");
            }

            return $testConnect['httpCode'] === 200;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * PurchaserRequests
     *
     * @param string $storeCode
     * @param \Magestore\Webpos\Api\Data\Checkout\OrderInterface $order
     * @return \Magestore\WebposZippay\Api\Data\ZippayErrorInterface|ZippayPurchaseResponseInterface
     */
    public function purchaserRequests($storeCode, $order)
    {
        /**
         * @var ZippayPurchaseResponseInterface $purchaseResponse
         */
        $purchaseResponse = $this->zippayPurchaseResponseFactory->create();

        /** @var \Magestore\Webpos\Api\Data\Checkout\Order\PaymentInterface[] $payments */
        $payments = $order->getPayments();
        $zipPaymentIndex = array_search(ZippayPaymentIntegration::CODE, array_column($payments, 'method'));
        /** @var \Magestore\Webpos\Api\Data\Checkout\Order\PaymentInterface $zipPayment */
        $zipPayment = $payments[$zipPaymentIndex];

        $webposLocationId = $order->getPosLocationId();
        $zipLocationId = $this->getZipLocationId($webposLocationId);

        if (!$zipLocationId) {
            $purchaseResponse->setError(
                $this->makeError(
                    [
                        'message' => self::STORE_LOCATION_EXCEPTION_MESSAGE
                    ]
                )
            );
            return $purchaseResponse;
        }

        $amountPaid = $zipPayment->getAmountPaid();
        $items = [
            [
                "name" => [],
                "quantity" => 1,
                "amount" => $amountPaid,
                "sku" => [],
                "refCode" => []
            ]
        ];

        foreach ($order->getItems() as $item) {
            $items[0]['name'][] = $item->getQtyOrdered() . ' x ' . $item->getName();
            $items[0]['sku'][] = $item->getSku();
            $items[0]['refCode'][] = $item->getProductId();
        }

        $items[0]['name'] = $this->checkAndCut(implode(", ", $items[0]['name']));
        $items[0]['sku'] = $this->checkAndCut(implode(", ", $items[0]['sku']));
        $items[0]['refCode'] = $this->checkAndCut(implode(", ", $items[0]['refCode']), 50);

        $payload = [
            "originator" => [
                "locationId" => $zipLocationId,
                "deviceRefCode" => $order->getPosId(),
                "staffActor" => [
                    "refCode" => $order->getPosStaffId() ?: ''
                ]
            ],
            "refCode" => $order->getIncrementId(),
            "payAmount" => $amountPaid,
            "accountIdentifier" => [
                "method" => "token",
                "value" => $storeCode
            ],
            "requiresAck" => 'false',
            "order" => [
                "totalAmount" => $amountPaid,
                "shippingAmount" => 0,
                "taxAmount" => 0,
                "items" => $items
            ]
        ];

        $purchaseRequest = $this->callApi("/purchaserequests", "POST", json_encode($payload));

        $response = json_decode($purchaseRequest['response'], true);

        if (empty($response)) {
            /**
             * @var ZippayErrorInterface $error
             */
            $error = $this->zippayErrorFactory->create();
            $error->setMessage(self::UNKNOWN_EXCEPTION_MESSAGE);
            $purchaseResponse->setError($error);
            return $purchaseResponse;
        }

        $purchaseResponse->setData($response);

        if (!empty($response['message'])) {
            $purchaseResponse->setError($this->makeError($response));
        }

        return $purchaseResponse;
    }

    /**
     * Purchaser Requests Refund
     *
     * @param int $id
     * @param string $refCode
     * @param float $refundAmount
     * @param \Magestore\WebposZippay\Api\Data\ZippayOriginatorInterface $originator
     * @return \Magestore\WebposZippay\Api\Data\ZippayErrorFieldInterface|ZippayResponseInterface
     */
    public function purchaserRequestsRefund($id, $refCode, $refundAmount, $originator)
    {
        /**
         * @var ZippayResponseInterface $responseCancel
         */
        $responseRefund = $this->zippayResponseFactory->create();
        $response = $this->callApi(
            "/purchaserequests/" . $id . "/refund",
            "POST",
            json_encode(
                [
                    "refCode" => $refCode,
                    "refundAmount" => $refundAmount,
                    "originator" => [
                        "locationId" => $originator->getLocationId(),
                        "deviceRefCode" => $originator->getDeviceRefCode(),
                        "staffActor" => [
                            "refCode" => $originator->getStaffActor()->getRefCode(),
                        ]
                    ]
                ]
            )
        );

        if ($response['httpCode'] === 204) {
            $responseRefund->setError(0);
            return $responseRefund;
        }

        $response = json_decode($response['response'], true);

        if ($response) {
            $responseRefund->setError($this->makeError($response));
            return $responseRefund;
        }

        $responseRefund->setError(self::UNKNOWN_EXCEPTION_MESSAGE);
        return $responseRefund;
    }

    /**
     * Fetch Transaction
     *
     * @param float|string $id
     * @return ZippayPurchaseResponseInterface
     */
    public function fetchTransaction($id)
    {
        $fetchRequest = $this->callApi("/purchaserequests/" . $id);
        /**
         * @var ZippayPurchaseResponseInterface $fetchResponse
         */
        $fetchResponse = $this->zippayPurchaseResponseFactory->create();

        $response = json_decode($fetchRequest['response'], true);
        if ($fetchRequest['httpCode'] === 200) {
            $fetchResponse->setData($response);
            return $fetchResponse;
        }

        if ($fetchRequest['httpCode'] === 0) {
            $fetchResponse->setStatus(self::TIMEOUT_STATUS);
        }

        if (!empty($response['message'])) {
            $fetchResponse->setError($this->makeError($response));
            return $fetchResponse;
        }

        /**
         * @var ZippayErrorInterface $error
         */
        $error = $this->zippayErrorFactory->create();
        $error->setMessage(self::UNKNOWN_EXCEPTION_MESSAGE);
        $fetchResponse->setError($error);
        return $fetchResponse;
    }

    /**
     * Cancel Purchaser Requests
     *
     * @param float|string $refCode
     * @param \Magestore\WebposZippay\Api\Data\ZippayOriginatorInterface $originator
     * @return \Magestore\WebposZippay\Api\Data\ZippayErrorFieldInterface|ZippayResponseInterface
     */
    public function cancelPurchaserRequests($refCode, $originator)
    {
        /**
         * @var ZippayResponseInterface $responseCancel
         */
        $responseCancel = $this->zippayResponseFactory->create();

        $response = $this->callApi(
            "/purchaserequests/void",
            "POST",
            json_encode(
                [
                    "refCode" => $refCode,
                    "originator" => [
                        "locationId" => $originator->getLocationId(),
                        "deviceRefCode" => $originator->getDeviceRefCode(),
                        "staffActor" => [
                            "refCode" => $originator->getStaffActor()->getRefCode(),
                        ]
                    ]
                ]
            )
        );

        if ($response['httpCode'] === 204) {
            $responseCancel->setError(0);
            return $responseCancel;
        }

        $response = json_decode($response['response'], true);

        if ($response) {
            $responseCancel->setError($this->makeError($response));
            return $responseCancel;
        }

        $responseCancel->setError(self::UNKNOWN_EXCEPTION_MESSAGE);
        return $responseCancel;
    }

    /**
     * CancelRefundRequests
     *
     * @param int $id
     * @param string $refCode
     * @param float $refundAmount
     * @param \Magestore\WebposZippay\Api\Data\ZippayOriginatorInterface $originator
     * @return \Magestore\WebposZippay\Api\Data\ZippayErrorFieldInterface|ZippayResponseInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function cancelRefundRequests($id, $refCode, $refundAmount, $originator)
    {
        /**
         * @var ZippayResponseInterface $responseRefund
         */
        $responseRefund = $this->zippayResponseFactory->create();
        $response = $this->callApi(
            "/purchaserequests/" . $id . "/refund/void",
            "POST",
            json_encode(
                [
                    "refCode" => $refCode,
                    "originator" => [
                        "locationId" => $originator->getLocationId(),
                        "deviceRefCode" => $originator->getDeviceRefCode(),
                        "staffActor" => [
                            "refCode" => $originator->getStaffActor()->getRefCode(),
                        ]
                    ]
                ]
            )
        );

        if ($response['httpCode'] === 204) {
            $responseRefund->setError(0);
            return $responseRefund;
        }

        $response = json_decode($response['response'], true);

        if ($response) {
            $responseRefund->setError($this->makeError($response));
            return $responseRefund;
        }

        $responseRefund->setError(self::UNKNOWN_EXCEPTION_MESSAGE);
        return $responseRefund;
    }

    /**
     * Check And Cut
     *
     * @param string $string
     * @param int $max
     * @return string
     */
    private function checkAndCut($string, $max = 150)
    {
        if (strlen($string) <= $max) {
            return $string;
        }

        return substr($string, 0, $max - 3) . '...';
    }

    /**
     * Make Error
     *
     * @param array $errorData
     * @return ZippayErrorInterface
     */
    private function makeError($errorData)
    {
        /**
         * @var ZippayErrorInterface $error
         */
        $error = $this->zippayErrorFactory->create();
        $error->setData($errorData);

        if (!empty($response['items'])) {
            $errorItems = [];
            foreach ($response['items'] as $item) {
                $errorItems[] = $this->zippayErrorFieldFactory
                    ->create()
                    ->setData($item);
            }
            $error->setItems($errorItems);
        }

        return $error;
    }
}
