<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposStripeTerminal\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magestore\Webpos\Api\Data\Location\LocationInterface;
use Magestore\Webpos\Api\Location\LocationRepositoryInterface;
use Magestore\WebposStripeTerminal\Model\ConnectedReader\ConnectedReaderFactory;
use Magestore\WebposStripeTerminal\Model\Data\SaveConnectedReaderRequest;
use Magestore\WebposStripeTerminal\Api\ConnectedReaderRepositoryInterface;

/**
 * Class StripeTerminalService
 *
 * @package Magestore\WebposStripeTerminal\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StripeTerminalService implements \Magestore\WebposStripeTerminal\Api\StripeTerminalServiceInterface
{

    /**
     * @var \Magestore\WebposStripeTerminal\Helper\Data
     */
    protected $helper;
    /**
     * @var ConnectedReaderFactory
     */
    protected $factory;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * StripeTerminalService constructor.
     *
     * @param \Magestore\WebposStripeTerminal\Helper\Data $helper
     * @param ConnectedReaderFactory $factory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magestore\WebposStripeTerminal\Helper\Data $helper,
        ConnectedReaderFactory $factory,
        \Magento\Framework\App\RequestInterface $request,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->helper = $helper;
        $this->factory = $factory;
        $this->request = $request;
        $this->logger = $logger;
    }

    /**
     * Connection token
     *
     * @return \Magestore\WebposStripeTerminal\Api\Data\ConnectionTokenResponseInterface
     * @throws \Magento\Framework\Exception\StateException
     */
    public function connectionToken()
    {
        try {
            \Stripe\Stripe::setApiKey($this->helper->getSecretKey());
            $response = \Stripe\Terminal\ConnectionToken::create();
            /**
             * @var \Magestore\WebposStripeTerminal\Api\Data\ConnectionTokenResponseInterface $responseData
             */
            $responseData = ObjectManager::getInstance()->create(
                \Magestore\WebposStripeTerminal\Api\Data\ConnectionTokenResponseInterface::class
            );

            if ($response instanceof \Stripe\StripeObject) {
                $responseData->setData($response->toArray());

                return $responseData;
            }

            return $responseData;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __($e->getMessage())
            );
        }
    }

    /**
     * API create payment intent which auth card, no charge
     *
     * @param \Magestore\WebposStripeTerminal\Api\Data\CreatePaymentIntentRequestInterface $request
     * @return \Magestore\WebposStripeTerminal\Api\Data\CreatePaymentIntentResponseInterface|null
     * @throws \Magento\Framework\Exception\StateException
     */
    public function createPaymentIntent($request)
    {
        try {
            \Stripe\Stripe::setApiKey($this->helper->getSecretKey());

            $data = $request->getData();
            $data['payment_method_types'] = ['card_present'];
            $data['capture_method'] = 'manual';

            $response = \Stripe\PaymentIntent::create($data);

            if (empty($response)) {
                throw new \Magento\Framework\Exception\StateException(
                    __('Connection failed. Please contact admin to check the configuration of API.')
                );
            }

            /**
             * @var \Magestore\WebposStripeTerminal\Api\Data\CreatePaymentIntentResponseInterface $responseData
             */
            $responseData = ObjectManager::getInstance()->create(
                \Magestore\WebposStripeTerminal\Api\Data\CreatePaymentIntentResponseInterface::class
            );

            if ($response instanceof \Stripe\StripeObject) {
                $response = $response->toArray();
                $responseData->setIntent($response['id']);
                $responseData->setSecret($response['client_secret']);

                return $responseData;
            }

            return $responseData;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __($e->getMessage())
            );
        }
    }

    /**
     * Capture payment
     *
     * @param \Magestore\WebposStripeTerminal\Api\Data\CapturePaymentIntentRequestInterface $request
     * @return \Magestore\WebposStripeTerminal\Api\Data\CapturePaymentIntentResponseInterface
     * @throws \Magento\Framework\Exception\StateException
     */
    public function capturePaymentIntent($request)
    {
        try {
            \Stripe\Stripe::setApiKey($this->helper->getSecretKey());

            /** @var \Stripe\PaymentIntent $paymentIntent */
            $paymentIntent = \Stripe\PaymentIntent::retrieve(
                $request->getPaymentIntentId()
            );

            $response = $paymentIntent->capture();

            if (empty($response)) {
                throw new \Magento\Framework\Exception\StateException(
                    __('Connection failed. Please contact admin to check the configuration of API.')
                );
            }

            /**
             * @var \Magestore\WebposStripeTerminal\Api\Data\CapturePaymentIntentResponseInterface $responseData
             */
            $responseData = ObjectManager::getInstance()->create(
                \Magestore\WebposStripeTerminal\Api\Data\CapturePaymentIntentResponseInterface::class
            );

            if ($response instanceof \Stripe\StripeObject) {
                $response = $response->toArray();
                $responseData->setIntent($response['id']);
                $responseData->setSecret($response['client_secret']);

                return $responseData;
            }

            return $responseData;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __($e->getMessage())
            );
        }
    }

    /**
     * Register reader
     *
     * @param \Magestore\WebposStripeTerminal\Api\Data\RegisterReaderRequestInterface $request
     * @return \Magestore\WebposStripeTerminal\Api\Data\RegisterReaderResponseInterface
     * @throws \Magento\Framework\Exception\StateException
     */
    public function registerReader($request)
    {
        try {
            $secret = $this->helper->getSecretKey();
            $stripeLocationId = $this->getStripeLocationId($secret);
            if (!$stripeLocationId) {
                throw new \Magento\Framework\Exception\StateException(
                    __('Not Found Location!')
                );
            }
            \Stripe\Stripe::setApiKey($secret);
            $data = $request->getData();
            $data['location'] = $stripeLocationId;
            $response = \Stripe\Terminal\Reader::create($data);
            /**
             * @var \Magestore\WebposStripeTerminal\Api\Data\RegisterReaderResponseInterface $responseData
             */
            $responseData = ObjectManager::getInstance()->create(
                \Magestore\WebposStripeTerminal\Api\Data\RegisterReaderResponseInterface::class
            );

            if ($response instanceof \Stripe\StripeObject) {
                $responseData->setData($response->toArray());

                return $responseData;
            }

            return $responseData;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __($e->getMessage())
            );
        }
    }

    /**
     * Refund payment
     *
     * @param \Magestore\WebposStripeTerminal\Api\Data\RegisterReaderRequestInterface $request
     * @return \Magestore\WebposStripeTerminal\Api\Data\RefundPaymentIntentResponseInterface
     * @throws \Magento\Framework\Exception\StateException
     */
    public function refundPaymentIntent($request)
    {
        try {
            \Stripe\Stripe::setApiKey($this->helper->getSecretKey());
            /** @var \Stripe\PaymentIntent $paymentIntent */
            $paymentIntent = \Stripe\PaymentIntent::retrieve(
                $request->getPaymentIntentId()
            );

            /** @var \Stripe\Collection $charges */
            $charges = $paymentIntent->charges;

            if (!$charges->count()) {
                throw new \Magento\Framework\Exception\StateException(
                    __('Cannot found charge.')
                );
            }

            /** @var \Stripe\Charge $charge */
            $charge = array_values($charges->data)[0];

            $data = $request->getData();
            unset($data['payment_intent_id']);
            $data['charge'] = $charge->id;

            $response = \Stripe\Refund::create($data);
            /**
             * @var \Magestore\WebposStripeTerminal\Api\Data\RefundPaymentIntentResponseInterface $responseData
             */
            $responseData = ObjectManager::getInstance()->create(
                \Magestore\WebposStripeTerminal\Api\Data\RefundPaymentIntentResponseInterface::class
            );

            if ($response instanceof \Stripe\StripeObject) {
                $responseData->setData($response->toArray());

                return $responseData;
            }

            return $responseData;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __($e->getMessage())
            );
        }
    }

    /**
     * Create new connected reader
     *
     * @return ConnectedReader\ConnectedReader
     */
    public function createNewConnectedReader()
    {
        return $this->factory->create();
    }

    /**
     * Save connected reader
     *
     * @param \Magestore\WebposStripeTerminal\Api\Data\SaveConnectedReaderRequestInterface $request
     * @return \Magestore\WebposStripeTerminal\Api\Data\SaveConnectedReaderResponseInterface
     * @throws \Magento\Framework\Exception\StateException
     */
    public function saveConnectedReader($request)
    {
        try {
            /** @var \Magento\Framework\ObjectManagerInterface $objectManager */
            $objectManager = $this->helper->getObjectManager();
            /** @var ConnectedReaderRepositoryInterface $connectedReaderRepository */
            $connectedReaderRepository = $objectManager->create(
                ConnectedReaderRepositoryInterface::class
            );
            try {
                /** @var \Magestore\WebposStripeTerminal\Api\Data\ConnectedReaderInterface $connectedReader */
                $connectedReader = $connectedReaderRepository->getPosByPosId($request->getPosId());
            } catch (\Exception $e) {
                $connectedReader = $this->createNewConnectedReader();
            }

            if (!$connectedReader->getId()) {
                $connectedReader = $this->createNewConnectedReader();
            }

            /** @var SaveConnectedReaderRequest $request */
            $connectedReader->setPosId($request->getPosId());
            $connectedReader->setReaderId($request->getReaderId());
            $connectedReader->setReaderLabel($request->getReaderLabel());
            $connectedReader->setIpAddress($request->getIpAddress());
            $connectedReader->setSerialNumber($request->getSerialNumber());
            $connectedReaderRepository->save($connectedReader);
            /**
             * @var \Magestore\WebposStripeTerminal\Api\Data\SaveConnectedReaderResponseInterface $responseData
             */
            $responseData = $objectManager->create(
                \Magestore\WebposStripeTerminal\Api\Data\SaveConnectedReaderResponseInterface::class
            );
            $responseData->setData($request->getData());

            return $responseData;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __($e->getMessage())
            );
        }
    }

    /**
     * Get is enable
     *
     * @return bool
     */
    public function isEnable()
    {
        return $this->helper->validateRequiredSDK() && $this->helper->isEnabled();
    }

    /**
     * Validate env
     *
     * @return bool
     * @throws LocalizedException
     */
    public function validateEnv()
    {
        if (!$this->helper->validateRequiredSDK()) {
            throw new LocalizedException(
                __('Stripe SDK not found, please go to the configuration to get the instruction to install the SDK')
            );
        }

        if (!$this->helper->isEnabled()) {
            throw new LocalizedException(__('Stripe Verifone P400 is disabled'));
        }

        if (empty($this->helper->getSecretKey())) {
            throw new LocalizedException(__('Secret key are required'));
        }

        return true;
    }

    /**
     * Test connect stripe API
     *
     * @return bool
     * @throws \Exception
     */
    public function connectToApi()
    {
        \Stripe\Stripe::setApiKey($this->helper->getSecretKey());
        try {
            if ($this->testCreate() instanceof \Stripe\Charge) {
                return true;
            }
        } catch (\Exception $e) {
            $this->logger->info('Connection failed. Please contact admin to check the configuration of API.');
        }

        throw new LocalizedException(__('Connection failed. Please contact admin to check the configuration of API.'));
    }

    /**
     * Test create stripe payment
     *
     * @return \Stripe\Charge
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function testCreate()
    {
        $card = [
            'number'    => '4242424242424242',
            'exp_month' => 5,
            'exp_year'  => date('Y') + 1,
        ];

        return \Stripe\Charge::create(
            [
                'amount'   => 100,
                'currency' => 'usd',
                'card'     => $card,
            ]
        );
    }

    /**
     * Get stripe Location id
     *
     * @param  string $secret
     * @return bool|string
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function getStripeLocationId($secret = null)
    {
        if (!$this->helper->validateRequiredSDK()) {
            return false;
        }

        $currentSession = $this->helper->getCurrentSession();

        if (!$currentSession) {
            $locationId = $this->request->getParam(
                \Magestore\Webpos\Model\Checkout\PosOrder::PARAM_ORDER_LOCATION_ID
            );
        } else {
            $locationId = $currentSession->getLocationId();
        }

        if (!$locationId) {
            return false;
        }

        /** @var LocationRepositoryInterface $locationRepository */
        $locationRepository = $this->helper->getObjectManager()->create(LocationRepositoryInterface::class);
        /** @var LocationInterface $location */
        $location = $locationRepository->getById($locationId);

        $secret = $secret ?: $this->helper->getSecretKey();

        if (empty($secret)) {
            return false;
        }

        if ($stripeLocation = $this->getStripeLocation($location, $secret)) {
            return $stripeLocation->id;
        }

        $stripeLocation = $this->createLocation($location, $secret);

        return $stripeLocation->id;
    }

    /**
     * Get stripe location
     *
     * @param LocationInterface $location
     * @param string $secret
     * @return \Stripe\Terminal\Location|bool
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function getStripeLocation($location, $secret = null)
    {
        $secret = $secret ?: $this->helper->getSecretKey();
        \Stripe\Stripe::setApiKey($secret);
        $registeredLocations = \Stripe\Terminal\Location::all();
        $stripeLocation = false;
        foreach ($registeredLocations as $registeredLocation) {
            if ($registeredLocation->display_name === $location->getName()) {
                $stripeLocation = $registeredLocation;
                break;
            }
        }

        return $stripeLocation;
    }

    /**
     * Create location
     *
     * @param LocationInterface $location
     * @param string $secret
     * @return \Stripe\Terminal\Location
     * @throws \Magento\Framework\Exception\StateException
     */
    public function createLocation($location, $secret = null)
    {
        try {
            $secret = $secret ?: $this->helper->getSecretKey();
            \Stripe\Stripe::setApiKey($secret);
            $response = \Stripe\Terminal\Location::create(
                [
                    'display_name' => $location->getName(),
                    'address'      => [
                        'line1'       => $location->getStreet(),
                        'city'        => $location->getCity(),
                        'country'     => $location->getCountryId() ? : 'US',
                        'postal_code' => $location->getPostcode(),
                    ],
                ]
            );

            return $response;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __($e->getMessage())
            );
        }
    }
}
