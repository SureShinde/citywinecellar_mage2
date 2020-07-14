<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosSampleData\Model;
/**
 * Class Currency
 * @package Magestore\Webpos\Model\Config\Data
 */
class ConfigRepository implements \Magestore\PosSampleData\Api\ConfigRepositoryInterface
{

    /**
     * Application config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_appConfig;
    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;

    /**
     * ConfigRepository constructor.
     * @param \Magento\Framework\Cache\FrontendInterface $cache
     * @param \Magento\Framework\App\Config\ReinitableConfigInterface $config
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     */
    public function __construct(
        \Magento\Framework\App\Config\ReinitableConfigInterface $config,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
    ){
        $this->_appConfig = $config;
        $this->configWriter = $configWriter;
    }

    public function changeConfig(\Magestore\PosSampleData\Api\Data\ConfigInterface $config){
        $this->configWriter->save($config->getPath(), $config->getValue(), $config->getScope(), $config->getScopeId());

        // re-init configuration
        $this->_appConfig->reinit();
        return true;
    }
}