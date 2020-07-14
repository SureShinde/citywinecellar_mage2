<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposTracking\Model\Service;

/**
 * Class TrackingService
 *
 * @package Magestore\WebposTracking\Model\Service
 */
class TrackingService
{
    const CUSTOMER_ID_PATH = "webpos/support/customer_id";
    const ENABLE_SUPPORT = "webpos/support/enable_tracking";
    const LAST_SYNCING_ORDER_ID = "webpos/support/last_syncing_order_id";
    const IS_SHOW_NOTIFICATION_PATH = "webpos/support/is_show_notification";

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;
    /**
     * @var \Magestore\WebposTracking\Helper\Data
     */
    protected $helper;
    /**
     * @var \Magestore\WebposTracking\Log\Logger
     */
    protected $logger;
    /**
     * @var \Magento\Framework\HTTP\ZendClientFactory
     */
    protected $httpClientFactory;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * TrackingService constructor.
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magestore\WebposTracking\Helper\Data $helper
     * @param \Magestore\WebposTracking\Log\Logger $logger
     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magestore\WebposTracking\Helper\Data $helper,
        \Magestore\WebposTracking\Log\Logger $logger,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->localeDate = $localeDate;
        $this->helper = $helper;
        $this->logger = $logger;
        $this->httpClientFactory = $httpClientFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Check if tracking function is enabled
     *
     * @return bool
     */
    public function isTrackingEnable()
    {
        return (bool)$this->helper->getConfig(self::ENABLE_SUPPORT);
    }

    /**
     * Count total order created on POS
     *
     * @return int
     */
    public function getTotalCreatedOrderOnPos()
    {
        $lastOrderId = (int)$this->helper->getConfigWithoutCache(self::LAST_SYNCING_ORDER_ID);

        $orderCollection = $this->orderCollectionFactory->create();
        $orderCollection->addFieldToFilter('entity_id', ['gt' => $lastOrderId]);
        $orderCollection->getSelect()->where('pos_location_id IS NOT NULL');

        $number = $orderCollection->getSize();
        return $number;
    }

    /**
     * Log last tracked order id
     */
    public function setLastOrderId()
    {
        $orderCollection = $this->orderCollectionFactory->create();
        $orderCollection->getSelect()->order('entity_id DESC');
        $lastItem = $orderCollection->getFirstItem();
        if ($lastItem->getId()) {
            $this->helper->setConfig(self::LAST_SYNCING_ORDER_ID, $lastItem->getId());
        }
    }

    /**
     * Send tracking API
     *
     * @return bool
     */
    public function sendTrackingApi()
    {
        try {
            $orderCount = $this->getTotalCreatedOrderOnPos();
            $this->setLastOrderId();

            // send API
            /** @var \Magento\Framework\HTTP\ZendClient $client */
            $client = $this->httpClientFactory->create();
            $url = 'https://tracking.magestore.com/v1/track/order';
            $params = [
                'customer_id' => $this->helper->getConfig(self::CUSTOMER_ID_PATH),
                'domain' => $this->storeManager->getStore()->getBaseUrl(),
                'order_quant' => $orderCount
            ];
            $client->setUri($url);
            $client->setHeaders(['Content-Type: application/json']);
            $client->setMethod(\Zend_Http_Client::POST);
            $client->setRawData(json_encode($params));

            $result = $client->request()->getBody();

            $this->logTrackingAction($orderCount);
            $this->logger->info($result);
        } catch (\Exception $e) {
            $this->logger->info($this->localeDate->date()->format('Y-m-d H:i:s'));
            $this->logger->info($e->getTraceAsString());
        }

        return true;
    }

    /**
     * Log tracking information
     *
     * @param int $orderCount
     */
    public function logTrackingAction($orderCount)
    {
        $this->logger->info($this->localeDate->date()->format('Y-m-d H:i:s'));
        $this->logger->info('Total orders: ' . $orderCount);
        $this->logger->info(
            'Last tracking order id: ' .
            (int)$this->helper->getConfigWithoutCache(self::LAST_SYNCING_ORDER_ID)
        );
    }
}
