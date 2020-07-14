<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposZippay\Model;

use Magestore\WebposZippay\Api\Data\ZippayErrorInterface;
use Magestore\WebposZippay\Api\Data\ZippayPurchaseResponseInterface;

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
     * @param \Magestore\WebposZippay\Helper\Data $zippay
     * @param \Magestore\WebposZippay\Model\Data\ZippayErrorFactory $zippayErrorFactory
     * @param \Magestore\WebposZippay\Model\Data\ZippayErrorFieldFactory $zippayErrorFieldFactory
     * @param \Magestore\WebposZippay\Model\Data\ZippayPurchaseResponseFactory $zippayPurchaseResponseFactory
     * @param \Magestore\WebposZippay\Model\Data\ZippayResponseFactory $zippayResponseFactory
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
     * @return bool
     */
    public function isEnable(){
        $configs = $this->zippay->getZippayConfig();
        return $configs['enable'] && !empty($configs['api_url']) && !empty($configs['api_key'])?true:false;
    }

    /**
     * @return string
     */
    public function getConfigurationError(){
        $message = '';
        $configs = $this->zippay->getZippayConfig();
        if($configs['enable']){
            if(empty($configs['api_url']) || empty($configs['api_key'])){
                $message = __('Zippay application api url and api key are required');
            }
        }else{
            $message = __('Zippay integration is disabled');
        }
        return $message;
    }

    /**
     * @param $webposLocationId
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
     * @param $api_url
     * @param $api_key
     * @return array
     */
    private function testApi($api_url, $api_key) {

        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $api_url . "/purchaserequests",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_POSTFIELDS => "{}",
                CURLOPT_HTTPHEADER => array(
                    "authorization: Basic ". base64_encode($api_key. ":"),
                    "content-type: application/json"
                ),
            ));

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_error($curl);

            curl_close($curl);
        } catch (\Exception $e) {
            return array(
                'httpCode' => 500,
                'response' => false
            );
        }

        // time out
        if ($httpCode === 0) {
            return array(
                'httpCode' => $httpCode,
                'response' => '{
                    "message": "' . self::TIME_OUT_EXCEPTION_MESSAGE . '"
                }'
            );
        }

        return array(
            'httpCode' => $httpCode,
            'response' => $response
        );
    }

    /**
     * @param $endpoint
     * @param string $method
     * @param string $param
     * @param int $timeout
     * @return array
     */
    private function callApi($endpoint, $method = "GET", $param = "{}", $timeout = 30) {
        if (!$timeout) {
            return array(
                'httpCode' => 0,
                'response' => '{
                    "message": "' . self::TIME_OUT_EXCEPTION_MESSAGE . '"
                }'
            );
        }

        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->zippay->getApiUrl() . $endpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => $timeout,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_POSTFIELDS => $param,
                CURLOPT_HTTPHEADER => array(
                    "authorization: Basic ". base64_encode($this->zippay->getApiKey() . ":"),
                    "content-type: application/json"
                ),
            ));

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_error($curl);

            curl_close($curl);
        } catch (\Exception $e) {
            return array(
                'httpCode' => 500,
                'response' => false
            );
        }

        // time out
        if ($httpCode === 0) {
            return array(
                'httpCode' => $httpCode,
                'response' => '{
                    "message": "' . self::TIME_OUT_EXCEPTION_MESSAGE . '"
                }'
            );
        }

        return array(
            'httpCode' => $httpCode,
            'response' => $response
        );
    }

    /**
     * @param null|string $apiUrl
     * @param null|string $apiKey
     * @return bool
     */
    public function canConnectToApi($apiUrl = null, $apiKey = null)
    {
       try {
           /**
            *  test api
            */
           if ($apiUrl && $apiKey) {
               $testConnect = $this->testApi($apiUrl , $apiKey);
           } else {
               $testConnect = $this->callApi("/purchaserequests");
           }

           return $testConnect['httpCode'] === 200;
       } catch (\Exception $exception) {
           return false;
       }
    }


    /**
     * @param $storeCode
     * @param \Magestore\Webpos\Api\Data\Checkout\OrderInterface $order
     * @return \Magestore\WebposZippay\Api\Data\ZippayErrorInterface|\Magestore\WebposZippay\Api\Data\ZippayPurchaseResponseInterface
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
            $purchaseResponse->setError($this->makeError(array(
                'message' => self::STORE_LOCATION_EXCEPTION_MESSAGE
            )));
            return $purchaseResponse;
        }


        $amountPaid = $zipPayment->getAmountPaid();
        $items = array(
            array(
                "name" => array(),
                "quantity" => 1,
                "amount" => $amountPaid,
                "sku" => array(),
                "refCode" => array(),
            )
        );

        foreach ($order->getItems() as $item) {
            $items[0]['name'][] = $item->getQtyOrdered() . ' x ' . $item->getName();
            $items[0]['sku'][] = $item->getSku();
            $items[0]['refCode'][] = $item->getProductId();
        }

        $items[0]['name'] = $this->checkAndCut(implode(", ", $items[0]['name']));
        $items[0]['sku'] = $this->checkAndCut(implode(", ", $items[0]['sku']));
        $items[0]['refCode'] = $this->checkAndCut(implode(", ", $items[0]['refCode']), 50);

        $payload = array(
            "originator" => array(
                "locationId" => $zipLocationId,
                "deviceRefCode" => $order->getPosId(),
                "staffActor" => array(
                    "refCode" => $order->getPosStaffId() ? : ''
                )
            ),
            "refCode" => $order->getIncrementId(),
            "payAmount" => $amountPaid,
            "accountIdentifier" => array(
                "method" => "token",
                "value" => $storeCode
            ),
            "requiresAck" => 'false',
            "order" => array(
                "totalAmount" => $amountPaid,
                "shippingAmount" => 0,
                "taxAmount" => 0,
                "items" => $items
            )
        );

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
     * @param $id
     * @param $refCode
     * @param $refundAmount
     * @param \Magestore\WebposZippay\Api\Data\ZippayOriginatorInterface $originator
     * @return \Magestore\WebposZippay\Api\Data\ZippayErrorFieldInterface|\Magestore\WebposZippay\Api\Data\ZippayResponseInterface
     */
    public function purchaserRequestsRefund($id, $refCode, $refundAmount, $originator)
    {

        /**
         * @var \Magestore\WebposZippay\Api\Data\ZippayResponseInterface $responseCancel
         */
        $responseRefund = $this->zippayResponseFactory->create();
        $response = $this->callApi("/purchaserequests/" . $id . "/refund", "POST", json_encode(array(
            "refCode" => $refCode,
            "refundAmount" => $refundAmount,
            "originator" => array(
                "locationId" =>  $originator->getLocationId(),
                "deviceRefCode" =>  $originator->getDeviceRefCode(),
                "staffActor" => array(
                    "refCode" => $originator->getStaffActor()->getRefCode(),
                )
            )
        )));

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

    public function fetchTransaction($id)
    {

//        $fetchRequest = $this->callApi("/purchaserequests/". $id, "GET", "{}", 0);
        $fetchRequest = $this->callApi("/purchaserequests/". $id);
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
     * @param float|string $refCode
     * @param \Magestore\WebposZippay\Api\Data\ZippayOriginatorInterface $originator
     * @return \Magestore\WebposZippay\Api\Data\ZippayErrorFieldInterface|\Magestore\WebposZippay\Api\Data\ZippayResponseInterface
     */
    public function cancelPurchaserRequests($refCode, $originator)
    {

        /**
         * @var \Magestore\WebposZippay\Api\Data\ZippayResponseInterface $responseCancel
         */
        $responseCancel = $this->zippayResponseFactory->create();

        $response = $this->callApi("/purchaserequests/void", "POST", json_encode(array(
            "refCode" => $refCode,
            "originator" => array(
                "locationId" =>  $originator->getLocationId(),
                "deviceRefCode" =>  $originator->getDeviceRefCode(),
                "staffActor" => array(
                    "refCode" => $originator->getStaffActor()->getRefCode(),
                )
            )
        )));

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
     * @param $id
     * @param $refCode
     * @param $refundAmount
     * @param \Magestore\WebposZippay\Api\Data\ZippayOriginatorInterface $originator
     * @return \Magestore\WebposZippay\Api\Data\ZippayErrorFieldInterface|\Magestore\WebposZippay\Api\Data\ZippayResponseInterface
     */
    public function cancelRefundRequests($id, $refCode, $refundAmount, $originator)
    {
        /**
         * @var \Magestore\WebposZippay\Api\Data\ZippayResponseInterface $responseRefund
         */
        $responseRefund = $this->zippayResponseFactory->create();
        $response = $this->callApi("/purchaserequests/" . $id . "/refund/void", "POST", json_encode(array(
            "refCode" => $refCode,
            "originator" => array(
                "locationId" =>  $originator->getLocationId(),
                "deviceRefCode" =>  $originator->getDeviceRefCode(),
                "staffActor" => array(
                    "refCode" => $originator->getStaffActor()->getRefCode(),
                )
            )
        )));

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

    private function checkAndCut($string, $max = 150) {
        if (strlen($string) <= $max) {
            return $string;
        }

        return substr($string, 0, $max - 3) . '...';
    }

    /**
     * @param $errorData
     * @return ZippayErrorInterface
     */
    private function makeError($errorData) {
        /**
         * @var ZippayErrorInterface $error
         */

        $error = $this->zippayErrorFactory->create();
        $error->setData($errorData);

        if (!empty($response['items'])) {
            $errorItems = array();
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
