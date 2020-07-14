<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposZippay\Helper;

/**
 * Class Data
 * @package Magestore\WebposZippay\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     *
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;
    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $_enc;
    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Encryption\EncryptorInterface $enc
    ){
        $this->_enc = $enc;
        $this->_storeManager = $storeManager;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct($context);
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStore(){
        return $this->_storeManager->getStore();
    }

    /**
     * get store config
     *
     * @param string $path
     * @return string
     */
    public function getStoreConfig($path){
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return array
     */
    public function getZippayConfig() {
        $configData = array();
        $configItems = array(
            'enable',
            'api_key',
            'api_url',
        );
        foreach ($configItems as $configItem) {
            $configData[$configItem] = $this->getStoreConfig('webpos/payment/zippay/' . $configItem);
        }
        return $configData;
    }

    /**
     * @return bool
     */
    public function isEnableZippay(){
        $enable = $this->getStoreConfig('webpos/payment/zippay/enable');
        return ($enable == 1)?true:false;
    }

    /**
     * @return string
     */
    public function getPaymentTitle(){
        return $this->getStoreConfig('webpos/payment/zippay/title');
    }

    /**
     * @return string
     */
    public function getApiKey(){
        return $this->_enc->decrypt($this->getStoreConfig('webpos/payment/zippay/api_key'));
    }

    /**
     * @return string
     */
    public function getApiUrl(){
        return $this->getStoreConfig('webpos/payment/zippay/api_url');
    }

    /**
     * @return string
     */
    public function getLocation(){
        return $this->getStoreConfig('webpos/payment/zippay/location');
    }

    /**
     * @return array
     */
    public function getLocationMap(){

        $locationString = $this->getLocation() ?: '[]';
        $locationArray = array_values(json_decode($locationString, true) ?: array());
        $locations = array();

        foreach ($locationArray as $locationData) {
            if (empty($locationData['webpos_location']) || empty($locationData['location_id'])) continue;
            $locations[$locationData['webpos_location']] = $locationData['location_id'];
        }

        return $locations;
    }
}