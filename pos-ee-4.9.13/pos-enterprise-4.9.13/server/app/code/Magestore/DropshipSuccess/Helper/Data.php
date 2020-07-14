<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Helper;

/**
 * Class Data
 * @package Magestore\DropshipSuccess\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const SECTION_CONFIG_ORDER_TAG = 'dropshipsuccess/order';

    /**
     *
     * @param string $path
     * @return string
     */
    public function getStoreConfig($path){
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * check module enable
     *
     * @param
     * @return boolean
     */
    public function checkModuleEnable($moduleName){
        return $this->_moduleManager->isEnabled($moduleName);
    }

    /**
     *
     * @param string $path
     * @return string
     */
    public function getOrderConfig($path){
        return $this->getStoreConfig(self::SECTION_CONFIG_ORDER_TAG . '/' . $path);
    }
}