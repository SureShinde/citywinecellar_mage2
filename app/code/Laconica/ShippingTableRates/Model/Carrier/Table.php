<?php

namespace Laconica\ShippingTableRates\Model\Carrier;

use Amasty\ShippingTableRates\Helper\Data;
use Amasty\ShippingTableRates\Model\RateFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Fedex\Model\Carrier;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Webapi\Soap\ClientFactory;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Sales\Model\Order\Shipment;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Shipping\Model\Shipment\Request;
use Magento\Shipping\Model\Tracking\Result;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use SoapClient;
use Zend_Measure_Length;
use Zend_Measure_Weight;
use Amasty\ShippingTableRates\Model\ResourceModel\Label\CollectionFactory as ALabelCollectionFactory;
use Amasty\ShippingTableRates\Model\ResourceModel\Method\CollectionFactory as AMethodCollectionFactory;

class Table extends \Amasty\ShippingTableRates\Model\Carrier\Table
{

    /**
     * @var Carrier
     */
    protected $fedexCarrier;

    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var ClientFactory|mixed|null
     */
    protected $soapClientFactory;

    /**
     * @var string
     */
    protected $_shipServiceWsdl;

    /**
     * Table constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param ALabelCollectionFactory $labelCollectionFactory
     * @param AMethodCollectionFactory $methodCollectionFactory
     * @param RateFactory $rateFactory
     * @param StoreManagerInterface $storeManager
     * @param Data $helperData
     * @param State $state
     * @param Carrier $fedexCarrier
     * @param CollectionFactory $productCollectionFactory
     * @param Reader $configReader
     * @param ClientFactory|null $soapClientFactory
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        ALabelCollectionFactory $labelCollectionFactory,
        AMethodCollectionFactory $methodCollectionFactory,
        RateFactory $rateFactory,
        StoreManagerInterface $storeManager,
        Data $helperData,
        State $state,
        Carrier $fedexCarrier,
        CollectionFactory $productCollectionFactory,
        Reader $configReader,
        ClientFactory $soapClientFactory = null,
        array $data = []
    ) {
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $rateResultFactory,
            $rateMethodFactory,
            $labelCollectionFactory,
            $methodCollectionFactory,
            $rateFactory,
            $storeManager,
            $helperData,
            $state,
            $data
        );
        $this->fedexCarrier = $fedexCarrier;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->soapClientFactory = $soapClientFactory ?: ObjectManager::getInstance()->get(ClientFactory::class);
        $wsdlBasePath = $configReader->getModuleDir(Dir::MODULE_ETC_DIR, 'Magento_Fedex') . '/wsdl/';
        $this->_shipServiceWsdl = $wsdlBasePath . 'ShipService_v10.wsdl';
    }

    /**
     * @return bool
     */
    public function isShippingLabelsAvailable()
    {
        return true;
    }

    /**
     * @param DataObject|null $params
     * @return array|bool
     */
    public function getContainerTypes(DataObject $params = null)
    {
        if ($params) {
            $params->setMethod('FEDEX_GROUND');
        }
        return $this->fedexCarrier->getContainerTypes($params);
    }

    /**
     * @param string $field
     * @return false|mixed|string
     */
    public function getFedexConfigData($field)
    {
        if (empty($this->_code)) {
            return false;
        }
        $path = 'carriers/fedex/' . $field;

        return $this->_scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $this->getStore()
        );
    }

    /**
     * Retrieve config flag for store by field
     *
     * @param string $field
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     * @api
     */
    public function getFedexConfigFlag($field)
    {
        if (empty($this->_code)) {
            return false;
        }
        $path = 'carriers/fedex/' . $field;

        return $this->_scopeConfig->isSetFlag(
            $path,
            ScopeInterface::SCOPE_STORE,
            $this->getStore()
        );
    }

    /**
     * Defines payment type by request. Two values are available: RECIPIENT or SENDER.
     *
     * @param DataObject $request
     * @return string
     */
    private function getPaymentType(DataObject $request): string
    {
        return $request->getIsReturn() && $request->getShippingMethod() !== $this->fedexCarrier::RATE_REQUEST_SMARTPOST
            ? 'RECIPIENT'
            : 'SENDER';
    }

    /**
     * Return array of authenticated information
     *
     * @return array
     */
    protected function _getAuthDetails()
    {
        return [
            'WebAuthenticationDetail' => [
                'UserCredential' => [
                    'Key' => $this->getFedexConfigData('key'),
                    'Password' => $this->getFedexConfigData('password'),
                ],
            ],
            'ClientDetail' => [
                'AccountNumber' => $this->getFedexConfigData('account'),
                'MeterNumber' => $this->getFedexConfigData('meter_number'),
            ],
            'TransactionDetail' => [
                'CustomerTransactionId' => '*** Express Domestic Shipping Request v9 using PHP ***',
            ],
            'Version' => ['ServiceId' => 'ship', 'Major' => '10', 'Intermediate' => '0', 'Minor' => '0'],
        ];
    }

    /**
     * Do request to shipment
     *
     * @param Request $request
     * @return DataObject
     * @throws LocalizedException
     */
    public function requestToShipment($request)
    {
        $packages = $request->getPackages();
        if (!is_array($packages) || !$packages) {
            throw new LocalizedException(__('No packages for request'));
        }
        if ($request->getStoreId() != null) {
            $this->setStore($request->getStoreId());
        }
        $data = [];
        foreach ($packages as $packageId => $package) {
            $request->setPackageId($packageId);
            $request->setPackagingType($package['params']['container']);
            $request->setPackageWeight($package['params']['weight']);
            $request->setPackageParams(new DataObject($package['params']));
            $request->setPackageItems($package['items']);
            $result = $this->_doShipmentRequest($request);

            if ($result->hasErrors()) {
                $this->rollBack($data);
                break;
            } else {
                $data[] = [
                    'tracking_number' => $result->getTrackingNumber(),
                    'label_content' => $result->getShippingLabelContent(),
                ];
            }
            if (!isset($isFirstRequest)) {
                $request->setMasterTrackingId($result->getTrackingNumber());
                $isFirstRequest = false;
            }
        }

        $response = new DataObject(['info' => $data]);
        if ($result->getErrors()) {
            $response->setErrors($result->getErrors());
        }

        return $response;
    }

    /**
     * For multi package shipments. Delete requested shipments if the current shipment request is failed
     *
     * @param array $data
     *
     * @return bool
     */
    public function rollBack($data)
    {
        $requestData = $this->_getAuthDetails();
        $requestData['DeletionControl'] = 'DELETE_ONE_PACKAGE';
        foreach ($data as &$item) {
            $requestData['TrackingId'] = $item['tracking_number'];
            $client = $this->_createShipSoapClient();
            $client->deleteShipment($requestData);
        }

        return true;
    }

    /**
     * Do shipment request to carrier web service, obtain Print Shipping Labels and process errors in response
     *
     * @param DataObject $request
     * @return DataObject
     */
    protected function _doShipmentRequest(DataObject $request)
    {
        $this->_prepareShipmentRequest($request);
        $result = new DataObject();
        $client = $this->_createShipSoapClient();
        $requestClient = $this->_formShipmentRequest($request);
        $debugData['request'] = $this->filterDebugData($requestClient);
        $response = $client->processShipment($requestClient);

        if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR') {
            $shippingLabelContent = $response->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image;
            $trackingNumber = $this->getResultTrackingNumber(
                $response->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds
            );
            $result->setShippingLabelContent($shippingLabelContent);
            $result->setTrackingNumber($trackingNumber);
            $debugData['result'] = $client->__getLastResponse();
            $this->_debug($debugData);
        } else {
            $debugData['result'] = ['error' => '', 'code' => '', 'xml' => $client->__getLastResponse()];
            if (is_array($response->Notifications)) {
                foreach ($response->Notifications as $notification) {
                    $debugData['result']['code'] .= $notification->Code . '; ';
                    $debugData['result']['error'] .= $notification->Message . '; ';
                }
            } else {
                $debugData['result']['code'] = $response->Notifications->Code . ' ';
                $debugData['result']['error'] = $response->Notifications->Message . ' ';
            }
            $this->_debug($debugData);
            $result->setErrors($debugData['result']['error']);
        }
        $result->setGatewayResponse($client->__getLastResponse());

        return $result;
    }

    /**
     * Return Tracking Number
     *
     * @param array|object $trackingIds
     * @return array|string
     */
    private function getResultTrackingNumber($trackingIds)
    {
        return is_array($trackingIds) ? array_map(
            function ($val) {
                return $val->TrackingNumber;
            },
            $trackingIds
        ) : $trackingIds->TrackingNumber;
    }

    /**
     * Recursive replace sensitive fields in debug data by the mask
     *
     * @param string $data
     * @return string
     */
    protected function filterDebugData($data)
    {
        foreach (array_keys($data) as $key) {
            if (is_array($data[$key])) {
                $data[$key] = $this->filterDebugData($data[$key]);
            } elseif (in_array($key, $this->_debugReplacePrivateDataKeys)) {
                $data[$key] = self::DEBUG_KEYS_MASK;
            }
        }
        return $data;
    }

    /**
     * Create ship soap client
     *
     * @return SoapClient
     */
    protected function _createShipSoapClient()
    {
        return $this->_createSoapClient($this->_shipServiceWsdl, 1);
    }

    /**
     * Create soap client with selected wsdl
     *
     * @param string $wsdl
     * @param bool|int $trace
     * @return SoapClient
     */
    protected function _createSoapClient($wsdl, $trace = false)
    {
        $client = $this->soapClientFactory->create($wsdl, ['trace' => $trace]);
        $client->__setLocation(
            $this->getFedexConfigFlag(
                'sandbox_mode'
            ) ? $this->getFedexConfigData('sandbox_webservices_url') : $this->getFedexConfigData('production_webservices_url')
        );

        return $client;
    }

    /**
     * @param $type
     * @param string $code
     * @return array|false
     */
    public function getCode($type, $code = '')
    {
        return $this->fedexCarrier->getCode($type, $code);
    }

    /**
     * Prepare shipment request. Validate and correct request information
     *
     * @param DataObject $request
     * @return void
     */
    protected function _prepareShipmentRequest(DataObject $request)
    {
        $phonePattern = '/[\s\_\-\(\)]+/';
        $phoneNumber = $request->getShipperContactPhoneNumber();
        $phoneNumber = preg_replace($phonePattern, '', $phoneNumber);
        $request->setShipperContactPhoneNumber($phoneNumber);
        $phoneNumber = $request->getRecipientContactPhoneNumber();
        $phoneNumber = preg_replace($phonePattern, '', $phoneNumber);
        $request->setRecipientContactPhoneNumber($phoneNumber);
    }

    /**
     * Return delivery confirmation types of carrier
     *
     * @param DataObject|null $params
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getDeliveryConfirmationTypes(DataObject $params = null)
    {
        return $this->fedexCarrier->getDeliveryConfirmationTypes($params);
    }

    /**
     * Form array with appropriate structure for shipment request
     *
     * @param DataObject $request
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _formShipmentRequest(DataObject $request)
    {
        if ($request->getReferenceData()) {
            $referenceData = $request->getReferenceData() . $request->getPackageId();
        } else {
            $referenceData = 'Order #' .
                $request->getOrderShipment()->getOrder()->getIncrementId() .
                ' P' .
                $request->getPackageId();
        }
        $packageParams = $request->getPackageParams();
        $customsValue = $packageParams->getCustomsValue();
        $height = $packageParams->getHeight();
        $width = $packageParams->getWidth();
        $length = $packageParams->getLength();
        $weightUnits = $packageParams->getWeightUnits() == Zend_Measure_Weight::POUND ? 'LB' : 'KG';
        $unitPrice = 0;
        $itemsQty = 0;
        $itemsDesc = [];
        $countriesOfManufacture = [];
        $productIds = [];
        $packageItems = $request->getPackageItems();
        foreach ($packageItems as $itemShipment) {
            $item = new DataObject();
            $item->setData($itemShipment);

            $unitPrice += $item->getPrice();
            $itemsQty += $item->getQty();

            $itemsDesc[] = $item->getName();
            $productIds[] = $item->getProductId();
        }

        // get countries of manufacture
        $productCollection = $this->_productCollectionFactory->create()->addStoreFilter(
            $request->getStoreId()
        )->addFieldToFilter(
            'entity_id',
            ['in' => $productIds]
        )->addAttributeToSelect(
            'country_of_manufacture'
        );
        foreach ($productCollection as $product) {
            $countriesOfManufacture[] = $product->getCountryOfManufacture();
        }

        $paymentType = $this->getPaymentType($request);
        $optionType = $request->getShippingMethod() == $this->fedexCarrier::RATE_REQUEST_SMARTPOST
            ? 'SERVICE_DEFAULT' : $packageParams->getDeliveryConfirmation();
        $requestClient = [
            'RequestedShipment' => [
                'ShipTimestamp' => time(),
                'DropoffType' => $this->getFedexConfigData('dropoff'),
                'PackagingType' => $request->getPackagingType(),
                'ServiceType' => 'FEDEX_GROUND', //CUSTOM CODE
                'Shipper' => [
                    'Contact' => [
                        'PersonName' => $request->getShipperContactPersonName(),
                        'CompanyName' => $request->getShipperContactCompanyName(),
                        'PhoneNumber' => $request->getShipperContactPhoneNumber(),
                    ],
                    'Address' => [
                        'StreetLines' => [$request->getShipperAddressStreet()],
                        'City' => $request->getShipperAddressCity(),
                        'StateOrProvinceCode' => $request->getShipperAddressStateOrProvinceCode(),
                        'PostalCode' => $request->getShipperAddressPostalCode(),
                        'CountryCode' => $request->getShipperAddressCountryCode(),
                    ],
                ],
                'Recipient' => [
                    'Contact' => [
                        'PersonName' => $request->getRecipientContactPersonName(),
                        'CompanyName' => $request->getRecipientContactCompanyName(),
                        'PhoneNumber' => $request->getRecipientContactPhoneNumber(),
                    ],
                    'Address' => [
                        'StreetLines' => [$request->getRecipientAddressStreet()],
                        'City' => $request->getRecipientAddressCity(),
                        'StateOrProvinceCode' => $request->getRecipientAddressStateOrProvinceCode(),
                        'PostalCode' => $request->getRecipientAddressPostalCode(),
                        'CountryCode' => $request->getRecipientAddressCountryCode(),
                        'Residential' => (bool)$this->getFedexConfigData('residence_delivery'),
                    ],
                ],
                'ShippingChargesPayment' => [
                    'PaymentType' => $paymentType,
                    'Payor' => [
                        'AccountNumber' => $this->getFedexConfigData('account'),
                        'CountryCode' => $this->_scopeConfig->getValue(
                            Shipment::XML_PATH_STORE_COUNTRY_ID,
                            ScopeInterface::SCOPE_STORE,
                            $request->getStoreId()
                        ),
                    ],
                ],
                'LabelSpecification' => [
                    'LabelFormatType' => 'COMMON2D',
                    'ImageType' => 'PNG',
                    'LabelStockType' => 'PAPER_8.5X11_TOP_HALF_LABEL',
                ],
                'RateRequestTypes' => ['ACCOUNT'],
                'PackageCount' => 1,
                'RequestedPackageLineItems' => [
                    'SequenceNumber' => '1',
                    'Weight' => ['Units' => $weightUnits, 'Value' => $request->getPackageWeight()],
                    'CustomerReferences' => [
                        'CustomerReferenceType' => 'CUSTOMER_REFERENCE',
                        'Value' => $referenceData,
                    ],
                    'SpecialServicesRequested' => [
                        'SpecialServiceTypes' => 'SIGNATURE_OPTION',
                        'SignatureOptionDetail' => ['OptionType' => $optionType],
                    ],
                ],
            ],
        ];

        // for international shipping
        if ($request->getShipperAddressCountryCode() != $request->getRecipientAddressCountryCode()) {
            $requestClient['RequestedShipment']['CustomsClearanceDetail'] = [
                'CustomsValue' => ['Currency' => $request->getBaseCurrencyCode(), 'Amount' => $customsValue],
                'DutiesPayment' => [
                    'PaymentType' => $paymentType,
                    'Payor' => [
                        'AccountNumber' => $this->getFedexConfigData('account'),
                        'CountryCode' => $this->_scopeConfig->getValue(
                            Shipment::XML_PATH_STORE_COUNTRY_ID,
                            ScopeInterface::SCOPE_STORE,
                            $request->getStoreId()
                        ),
                    ],
                ],
                'Commodities' => [
                    'Weight' => ['Units' => $weightUnits, 'Value' => $request->getPackageWeight()],
                    'NumberOfPieces' => 1,
                    'CountryOfManufacture' => implode(',', array_unique($countriesOfManufacture)),
                    'Description' => implode(', ', $itemsDesc),
                    'Quantity' => ceil($itemsQty),
                    'QuantityUnits' => 'pcs',
                    'UnitPrice' => ['Currency' => $request->getBaseCurrencyCode(), 'Amount' => $unitPrice],
                    'CustomsValue' => ['Currency' => $request->getBaseCurrencyCode(), 'Amount' => $customsValue],
                ],
            ];
        }

        if ($request->getMasterTrackingId()) {
            $requestClient['RequestedShipment']['MasterTrackingId'] = $request->getMasterTrackingId();
        }

        if ($request->getShippingMethod() == $this->fedexCarrier::RATE_REQUEST_SMARTPOST) {
            $requestClient['RequestedShipment']['SmartPostDetail'] = [
                'Indicia' => (double)$request->getPackageWeight() >= 1 ? 'PARCEL_SELECT' : 'PRESORTED_STANDARD',
                'HubId' => $this->getFedexConfigData('smartpost_hubid'),
            ];
        }

        // set dimensions
        if ($length || $width || $height) {
            $requestClient['RequestedShipment']['RequestedPackageLineItems']['Dimensions'] = [
                'Length' => $length,
                'Width' => $width,
                'Height' => $height,
                'Units' => $packageParams->getDimensionUnits() == Zend_Measure_Length::INCH ? 'IN' : 'CM',
            ];
        }

        return $this->_getAuthDetails() + $requestClient;
    }

    /**
     * Get tracking information
     *
     * @param string $tracking
     * @return string|false
     * @api
     */
    public function getTrackingInfo($tracking)
    {
        $result = $this->fedexCarrier->getTracking($tracking);

        if ($result instanceof Result) {
            $trackings = $result->getAllTrackings();
            if ($trackings) {
                return $trackings[0];
            }
        } elseif (is_string($result) && !empty($result)) {
            return $result;
        }

        return false;
    }
}
