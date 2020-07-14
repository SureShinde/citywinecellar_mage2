<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Service\Config;

use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\ShippingMethod as ShippingMethodOption;

/**
 * Class AbstractConfig
 * @package Magestore\PurchaseOrderSuccess\Service\Config
 */
class AbstractConfig
{
    const PURCHASE_ORDER_CONFIG_PATH = 'purchaseordersuccess/shipping_method/shipping_method';

    /**
     * @var string
     */
    protected $errorMessage = 'Please enter shipping method.';

    /**
     * @var \Magento\Config\Model\ConfigFactory
     */
    protected $configFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    
    public function __construct(
        \Magento\Config\Model\ConfigFactory $configFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->configFactory = $configFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface $purchaseOrder
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface
     * @throws \Exception
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function saveConfig(\Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface $purchaseOrder){
        $newConfig = $this->initNewConfig($purchaseOrder);
        if(!$newConfig || $newConfig == '')
            throw new \Magento\Framework\Exception\CouldNotSaveException(__($this->errorMessage));
        $this->initAllConfigValue($purchaseOrder, $newConfig);
        return $purchaseOrder;
    }

    /**
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface $purchaseOrder
     * @return string
     */
    public function initNewConfig(\Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface $purchaseOrder){
        if(!$purchaseOrder->getShippingMethod())
            $purchaseOrder->setShippingMethod(ShippingMethodOption::OPTION_NONE_VALUE);
        if($purchaseOrder->getShippingMethod() == ShippingMethodOption::OPTION_NEW_VALUE)
            $purchaseOrder->setShippingMethod($purchaseOrder->getData('new_shipping_method'));
        return $purchaseOrder->getShippingMethod();
    }

    /**
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface $purchaseOrder
     * @return bool
     */
    public function isNoneValueMethod($purchaseOrder){
        if($purchaseOrder->getShippingMethod() == ShippingMethodOption::OPTION_NONE_VALUE)
            return true;
        return false;
    }

    /**
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface $purchaseOrder
     * @param string $newConfig
     * @throws \Exception
     */
    public function initAllConfigValue($purchaseOrder, $newConfig){
        if($this->isNoneValueMethod($purchaseOrder))
            return $this;
        $configValue = $this->scopeConfig->getValue(static::PURCHASE_ORDER_CONFIG_PATH);
        if(!is_array($configValue)){
            $configValue = unserialize($configValue);
            $configValue = !$configValue?[]:$configValue;
            $currentConfig = $this->searchSubArray($configValue, 'name', $newConfig);
            if(!is_array($currentConfig)){
                $this->saveNewConfig($configValue, $newConfig);
            }
        }
    }

    /**
     * @param array $configValue
     * @param $newConfig
     * @throws \Exception
     */
    public function saveNewConfig($configValue, $newConfig){
        $date = new \DateTime();
        $configValue[$date->getTimestamp()] = $this->generateNewConfig($newConfig);
        $config = $this->configFactory->create();
        $config->setDataByPath(
            static::PURCHASE_ORDER_CONFIG_PATH, $configValue
        );
        try{
            $config->save();
        }catch(\Exception $e){
            throw $e;
        }
    }

    /**
     * Generate new element config.
     * 
     * @param string $newConfig
     * @return array
     */
    public function generateNewConfig($newConfig){
        return [
            'name' => $newConfig,
            'status' => \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Form\Field\Status::ENABLE_VALUE
        ];
    }

    /**
     * Search an subarray with key and value itself
     *
     * @param array $array
     * @param string $key
     * @param mixed $value
     * @return array|null
     */
    public function searchSubArray($array, $key, $value) {
        foreach ($array as $subarray){
            if (isset($subarray[$key]) && $subarray[$key] == $value)
                return $subarray;
        }
    }
}