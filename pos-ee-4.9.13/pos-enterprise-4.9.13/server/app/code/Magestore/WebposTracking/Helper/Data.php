<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposTracking\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Data
 *
 * @package Magestore\WebposTracking\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;
    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $_resourceConfig;
    /**
     * @var \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory
     */
    protected $configCollectionFactory;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Config\Model\ResourceModel\Config $resourceConfig
     * @param \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory $configCollectionFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory $configCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ) {
        parent::__construct($context);
        $this->objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->_resourceConfig = $resourceConfig;
        $this->configCollectionFactory = $configCollectionFactory;
        $this->localeDate = $localeDate;
    }

    /**
     * Get config data
     *
     * @param string $key
     * @param string $scope
     * @param int|string $store
     * @return mixed
     */
    public function getConfig($key, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $store = null)
    {
        return $this->scopeConfig->getValue(
            $key,
            $scope,
            $store
        );
    }

    /**
     * Get system config without cache
     *
     * @param string $key
     * @param string $scope
     * @param int $scopeId
     * @return mixed
     */
    public function getConfigWithoutCache($key, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0)
    {
        /** @var \Magento\Config\Model\ResourceModel\Config\Data\Collection $collection */
        $collection = $this->configCollectionFactory->create();
        $collection->addFieldToFilter('scope', $scope);
        $collection->addFieldToFilter('scope_id', $scopeId);
        $collection->addFieldToFilter('path', ['like' => $key]);
        $config = $collection->getFirstItem();
        return $config ? $config['value'] : null;
    }

    /**
     * Set system config
     *
     * @param string $key
     * @param string $value
     * @param string $scope
     * @param int $scopeId
     * @return bool
     */
    public function setConfig($key, $value, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0)
    {
        $this->_resourceConfig->saveConfig(
            $key,
            $value,
            $scope,
            $scopeId
        );

        return true;
    }
}
