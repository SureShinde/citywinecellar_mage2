<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosSampleData\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Currency
 * @package Magestore\Webpos\Model\Config\Data
 */
class Config extends \Magento\Framework\DataObject implements \Magestore\PosSampleData\Api\Data\ConfigInterface
{

    /**
     * @inheritdoc
     */
    public function getPath() {
        return $this->getData(self::PATH);
    }	
    /**
     * @inheritdoc
     */
    public function setPath($path) {
        return $this->setData(self::PATH, $path);
    }

    /**
     * @inheritdoc
     */
    public function getValue() {
        return $this->getData(self::VALUE);
    }	
    /**
     * @inheritdoc
     */
    public function setValue($value) {
        return $this->setData(self::VALUE, $value);
    }

    /**
     * @inheritdoc
     */
    public function getScope() {
        return $this->getData(self::SCOPE) ? $this->getData(self::SCOPE) : ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
    }	
    /**
     * @inheritdoc
     */
    public function setScope($scope) {
        return $this->setData(self::SCOPE, $scope);
    }

    /**
     * @inheritdoc
     */
    public function getScopeId() {
        return $this->getData(self::SCOPE_ID) ? $this->getData(self::SCOPE_ID) : 0;
    }	
    /**
     * @inheritdoc
     */
    public function setScopeId($scopeId) {
        return $this->setData(self::SCOPE_ID, $scopeId);
    }
}