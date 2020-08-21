<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposTyro\Helper;

/**
 * Helper Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const PAYMENT_CODE = 'tyro_integration';
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct($context);
    }

    /**
     * Get store
     *
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * Get store config
     *
     * @param string $path
     * @return string
     */
    public function getStoreConfig($path)
    {
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Tyro Config
     *
     * @return array
     */
    public function getTyroConfig()
    {
        $configData = [];
        $configItems = [
            'enable',
        ];
        foreach ($configItems as $configItem) {
            $configData[$configItem] = $this->getStoreConfig('webpos/payment/tyro/' . $configItem);
        }
        return $configData;
    }

    /**
     * Is Enable Tyro
     *
     * @return bool
     */
    public function isEnableTyro()
    {
        $enable = $this->getStoreConfig('webpos/payment/tyro/enable');
        return ($enable == 1) ? true : false;
    }

    /**
     * Get Payment Title
     *
     * @return string
     */
    public function getPaymentTitle()
    {
        return $this->getStoreConfig('webpos/payment/tyro/title');
    }

    /**
     * Get Api Key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->getStoreConfig('webpos/payment/tyro/api_key');
    }

    /**
     * GetMerchantId
     *
     * @return string
     */
    public function getMerchantId()
    {
        return $this->getStoreConfig('webpos/payment/tyro/merchant_id');
    }

    /**
     * GetMode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getStoreConfig('webpos/payment/tyro/mode');
    }
}
